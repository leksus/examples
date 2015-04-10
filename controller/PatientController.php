<?php

namespace App\AppBundle\Controller;

use App\AppBundle\Entity\Patient;
use App\AppBundle\Form\YesNoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/patient")
 */
class PatientController extends Controller
{
    const NOT_FOUND = 'Не существует пациента с id = ';

    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction()
    {

        return $this->render(
            "AppBundle:Patient:index.html.twig", array(
                "info" => ''
            ));

    }

    /**
     * Добавление нового пациента в систему
     *
     * @Route("/create", name="app_app_patient_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {        
        if(true === $this->get('security.authorization_checker')->isGranted('ROLE_STATISTIC')||
            true === $this->get('security.authorization_checker')->isGranted('ROLE_DOCTOR')) {
            $patient = new Patient();

            $formBuilder = $this->createFormBuilder($patient, array('csrf_protection' => false));
            $formBuilder
                ->add('surname', 'text', array('label' => 'Фамилия'))
                ->add('forename', 'text', array('label' => 'Имя'))
                ->add('patronymic', 'text', array('label' => 'Отчество', 'required' => ''))
                ->add('birthday', 'birthday', array('label' => 'Дата рождения'))
                ->add('address', 'textarea', array('label' => 'Адрес', 'required' => ''))
                ->add('disability', 'text', array('label' => 'Инвалидность'))
                ->add('admissionDiagnosis', 'text', array('label' => 'Диагноз при поступлении', 'required' => ''))
            ;

            $formBuilder
                ->add('notes', 'textarea', array('label' => 'Особые отметки', 'required' => ''))
                ->add('submit', 'submit', array('label' => 'Сохранить'));

            $form = $formBuilder->getForm();
            $form->handleRequest($request);

            if($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($patient);
                $em->flush();
               
                return $this->redirectToRoute('app_app_patient_show', array('id' => $patient->getId()));
            }

            return $this->render(
                'AppBundle:Patient:create.html.twig', array(
                'form' => $form->createView(),
                'info' => '',
        ));
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Отображение данных пациента по id
     *
     * @Route("/{id}", requirements={"id": "\d+"})
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $patient = $this->getDoctrine()->getRepository("AppBundle:Patient")->find($id);

        if(!$patient) {
            throw $this->createNotFoundException(
                "Пациент с id = $id отсутствует"
            );
        }

        $breadcrumbs = $this->get('white_october_breadcrumbs');

        $breadcrumbs->addItem("Назад к поиску", $this->get('router')->generate("app_app_patient_search"));
        $breadcrumbs->addItem($id);

        return $this->render(
            "AppBundle:Patient:show.html.twig", array(
                "info" => '',
                "patient" => $patient,
            ));
    }

    /**
     * Поиск пациента в системе
     *
     * @Route("/search")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $searchForm = $this->createFormBuilder()
            ->add('surname', 'text', array('label' => 'Фамилия', 'required' => ''))
            ->add('forename', 'text', array('label' => 'Имя', 'required' => ''))
            ->add('patronymic', 'text', array('label' => 'Отчество', 'required' => ''))
            //->add('birthday', 'birthday', array('label' => 'Дата рождения', 'required' => ''))
            ->add('submit', 'submit', array('label' => 'Поиск'))
            ->getForm()
        ;

        $searchForm->handleRequest($request);

        if($searchForm->isValid()) {

            $repository = $this->getDoctrine()->getRepository('AppBundle:Patient');

            $qb = $repository->createQueryBuilder('p')
                ->select('p')
                ->setMaxResults(20)
                ;

            $criteries = $searchForm->getData();

            foreach($criteries as $key => $criteria) {

                if(!$criteria) {
                    continue;
                } else {
                    $qb->andWhere("p.$key LIKE '$criteria%'");
                }

            }

            $qb->orderBy('p.id', 'DESC');

            $patients = $qb->getQuery()->getResult();

            return $this->render('AppBundle:Patient:search.html.twig', array(
                'form' => $searchForm->createView(),
                'patients' => $patients
            ));
        }

        return $this->render('AppBundle:Patient:search.html.twig', array(
            'form' => $searchForm->createView(),
            'patients' => ''
        ));
    }

    /**
     * Отображение всех записей пациента
     *
     * @Route("/{id}/records", requirements={ "id": "\d+" })
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recordsAction($id)
    {
        $patient = $this->getDoctrine()->getRepository('AppBundle:Patient')->find($id);

        if(!$patient) {
            throw $this->createNotFoundException("Не существует пациента с id = $id");
        }

        $records = $patient->getRecords();

        if(!$records) {
            $records = array();
        }

        return $this->render('AppBundle:Patient:records.html.twig', array(
            'patient' => $patient,
            'records' => $records,
            'info' => ''
        ));
    }

    /**
     * Редактирование данных пациента
     *
     * @Route("/{id}/edit", requirements={ "id": "\d+" })
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_STATISTIC') ||
            true === $this->get('security.authorization_checker')->isGranted('ROLE_MANAGER') ||
            true === $this->get('security.authorization_checker')->isGranted('ROLE_DOCTOR')) {

            $entityManager = $this->getDoctrine()->getManager();
            $patient = $entityManager->getRepository('AppBundle:Patient')->find($id);

            if (!$patient) {
                throw $this->createNotFoundException("Не существует пациента с id = $id");
            }

            $formBuilder = $this->createFormBuilder($patient, array('csrf_protection' => false));

            if (true === $this->get('security.authorization_checker')->isGranted('ROLE_STATISTIC')) {
                $formBuilder
                    ->add('surname', 'text', array('label' => 'Фамилия'))
                    ->add('forename', 'text', array('label' => 'Имя'))
                    ->add('patronymic', 'text', array('label' => 'Отчество', 'required' => ''))
                    ->add('birthday', 'birthday', array('label' => 'Дата рождения'))
                    ->add('address', 'textarea', array('label' => 'Адрес', 'required' => ''))
                    ->add('disability', 'text', array('label' => 'Инвалидность'))
                    ->add('admissionDiagnosis', 'text', array('label' => 'Диагноз при поступлении', 'required' => ''));
            }

            if (true === $this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')) {

                $templateAboutFamily = '';

                if ($patient->getAboutFamily() == '') {
                    $templateAboutFamily = $this->getDoctrine()->getRepository('AppBundle:Template')->find(1)->getBody(
                    );
                }

                $formBuilder
                    ->add('edu', 'text', array('label' => 'Образовательное учреждение', 'required' => ''))
                    ->add(
                        'aboutFamily',
                        'ckeditor',
                        array(
                            'label' => 'О семье',
                            'required' => '',
                            'data' => $patient->getAboutFamily() ? $patient->getAboutFamily() : $templateAboutFamily,
                            'attr' => ['rows' => 25]
                        )
                    );
            }

            if (true === $this->get('security.authorization_checker')->isGranted('ROLE_DOCTOR')) {
                $formBuilder
                    ->add('basicDiagnosis', 'textarea', array('label' => 'Основной диагноз', 'required' => ''))
                    ->add('conDiagnosis', 'textarea', array('label' => 'Сопутствующий диагноз', 'required' => ''))
                    ->add(
                        'intolerantDrugs',
                        'textarea',
                        array('label' => 'Непереносимость лекарственных средств', 'required' => '')
                    );
            }

            $formBuilder
                ->add('notes', 'textarea', array('label' => 'Особые отметки', 'required' => ''))
                ->add('submit', 'submit', array('label' => 'Сохранить'));

            $form = $formBuilder->getForm();
            $form->handleRequest($request);

            $breadcrumbs = $this->get('white_october_breadcrumbs');

            $breadcrumbs->addItem("Назад к пациенту", $this->get('router')->generate("app_app_patient_show", ['id' => $patient->getId()]));
            $breadcrumbs->addItem('_');

            if ($form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute("app_app_patient_show", ['id' => $id]);
            }

            return $this->render(
                "AppBundle:Patient:create.html.twig",
                array(
                    "info" => '',
                    "form" => $form->createView(),
                )
            );
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Удаление пациента из системы
     *
     * @Route("/{id}/delete", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        if(true === $this->get('security.authorization_checker')->isGranted('ROLE_STATISTIC')) {

            $em = $this->getDoctrine()->getManager();
            $patient = $em->getRepository('AppBundle:Patient')->find($id);

            if (!$patient) {
                throw $this->createNotFoundException("Не существует пациента с id = $id");
            }

            $form = $this->createForm(new YesNoType());

            $form->handleRequest($request);
            $data = $form->getData();

            if($data['choice'] == 'yes') {

                $em->remove($patient);
                $em->flush();

                $breadcrumbs = $this->get('white_october_breadcrumbs');

                $breadcrumbs->addItem("Назад к поиску", $this->get('router')->generate("app_app_patient_search"));
                $breadcrumbs->addItem('_');

                return $this->render('AppBundle:Default:yesno.html.twig', [
                    'form' => $this->createFormBuilder()->getForm()->createView(),
                    'info' => 'Пациент успешно удален'
                ]);
            }

            return $this->render('AppBundle:Default:yesno.html.twig', [
                'form' => $form->createView(),
                'info' => 'Внимание! При удалении пациента автоматически удаляются все карты и записи, связанные с ним'
            ]);

        } else {
            throw $this->createAccessDeniedException();
        }
    }
}
