<?php

namespace App\AppBundle\Controller;

use App\AppBundle\Entity\Attachment;
use App\AppBundle\Entity\Category;
use App\AppBundle\Entity\Record;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RecordController
 * @package App\AppBundle\Controller
 * @Route("/record")
 */
class RecordController extends Controller
{
    const NOT_FOUND = 'Не существует записи с id = ';

    /**
     * @Route("/create/{patientId}", requirements={ "patientId": "\d+" }, name="app_app_record_new")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $patientId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $patientId)
    {
        $patient = $this->getDoctrine()->getRepository('AppBundle:Patient')->find($patientId);

        if(!$patient) {
            throw $this->createNotFoundException();
        }

        $record = new Record();

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Card', 'c')
            ->where('c.patient = ?1')
            ->andWhere('c.discharge IS NULL')
            ->orderBy('c.datein', 'DESC')
            ->setParameter('1', $patientId);

        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('card', 'entity', [
                'label' => 'Карта',
                'class' => 'App\AppBundle\Entity\Card',
                'query_builder' => $qb,
            ])
            ->add('category', 'entity', array(
                'class' => 'App\AppBundle\Entity\Category',
                'property' => 'name',
                'label' => 'Тип записи',
                'placeholder' => 'Выберите тип записи'
            ))
            ->add('speciality', 'text', ['label' => 'Ваша специальность'])
            ->add('text', 'ckeditor', array('label' => 'Текст записи'))
            ->add('attachments', 'file', array('label' => 'Файл (не больше 2 мегабайт)', 'required' => ''))
            ->add('submit', 'submit', array('label' => 'Сохранить'))
            ->getForm()
        ;

        $form->handleRequest($request);

        $breadcrumbs = $this->get('white_october_breadcrumbs');

        $breadcrumbs->addItem("Назад к пациенту", $this->get('router')->generate("app_app_patient_show", ['id' => $patientId]));
        $breadcrumbs->addItem('_');

        if ($form->isValid()) {

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $record->setCardId($data['card']);
            $record->setPatientId($patient);
            $record->setCreatorId($this->getUser());
            $record->setCreatedAt(new \DateTime());
            $record->setCategory($data['category']);
            $record->setSpeciality($data['speciality']);
            $record->setText($data['text']);

            if($data['attachments']) {
                $attachment = new Attachment();
                $attachment->setFile($data['attachments']);
                $attachment->setRecord($record);
                try  {
                    $attachment->upload();
                } catch (\Exception $e) {
                    return $this->render(
                        "AppBundle:Record:create.html.twig",
                        array(
                            'form' => $form->addError(new FormError($e->getMessage()))->createView(),
                            'info' => '',
                            'patient' => $patient
                        )
                    );
                }

                $record->addAttachment($attachment);
                $em->persist($attachment);
            }

            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute('app_app_record_show', array('id' => $record->getId()));
        }

        return $this->render(
            "AppBundle:Record:create.html.twig",
            array(
                'form' => $form->createView(),
                'info' => '',
                'patient' => $patient
            )
        );
    }

    /**
     * @Route("/remove_attachment/{id}/", requirements={ "id": "\d+" })
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAttachmentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $attachment = $em->getRepository('AppBundle:Attachment')->find($id);

        $attachment->removeUpload();
        $em->remove($attachment);
        $em->flush();

        return $this->redirectToRoute('app_app_record_edit', ['id' => $attachment->getRecord()->getId()]);
    }

    /**
     * @Route("/{id}", requirements={ "id": "\d+" })
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);

        if(!$record) {
            throw $this->createNotFoundException(self::NOT_FOUND . $id);
        }

        $breadcrumbs = $this->get('white_october_breadcrumbs');

        $breadcrumbs->addItem($record->getPatientId()->getSurname(), $this->get('router')->generate("app_app_patient_show", ['id' => $record->getPatientId()->getId()]));
        $breadcrumbs->addItem('Вернуться в карту', $this->get('router')->generate("app_app_card_show", ['id' => $record->getCardId()->getId()]));
        $breadcrumbs->addItem("Запись " . $id);

        return $this->render('AppBundle:Record:show.html.twig', array(
            'record' => $record,
        ));
    }

    /**
     * @Route("/{id}/sign", requirements={ "id": "\d+" })
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);

        if(!$record) {
            throw $this->createNotFoundException(self::NOT_FOUND . $id);
        }

        $breadcrumbs = $this->get('white_october_breadcrumbs');

        $breadcrumbs->addItem("Назад к записи", $this->get('router')->generate("app_app_record_show", ['id' => $record->getId()]));
        $breadcrumbs->addItem('_');

        if($record->getCreatorId() == $this->getUser()) {

            $password = $request->get('password');

            if ($password === null) {
                return $this->render(
                    'AppBundle:Record:sign.html.twig',
                    array(
                        'error' => ''
                    )
                );
            }

            $encoder = $this->container->get('security.password_encoder');
            $user = $this->getUser();

            if ($encoder->isPasswordValid($user, $password)) {

                $record->setAuthorId($user);
                $record->setSignedAt(new \DateTime());

                $em->flush();

                return $this->redirectToRoute('app_app_record_show', array('id' => $id));
            } else {
                return $this->render(
                    'AppBundle:Record:sign.html.twig',
                    array(
                        'error' => 'Неверный пароль'
                    )
                );
            }
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/{id}/edit", requirements={ "id": "\d+" })
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);

        if(!$record) {
            throw $this->createNotFoundException(self::NOT_FOUND . $id);
        }

        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('category', 'entity', array(
                'class' => 'App\AppBundle\Entity\Category',
                'property' => 'name',
                'label' => 'Тип записи',
                'data' => $record->getCategory()))
            ->add('speciality', 'text', [
                'label' => 'Ваша специальность',
                'data' => $record->getSpeciality() ? $record->getSpeciality() : null])
            ->add('text', 'ckeditor', array(
                'label' => 'Текст записи',
                'attr' => ['rows' => '25'],
                'data' => $record->getText() ? $record->getText() : null))
            ->add('attachments', 'file', array('label' => 'Файл (не больше 2 мегабайт)', 'required' => ''))
            ->add('submit', 'submit', array('label' => 'Сохранить'))
            ->getForm()
        ;

        $form->handleRequest($request);

        $breadcrumbs = $this->get('white_october_breadcrumbs');

        $breadcrumbs->addItem("Назад в карту", $this->get('router')->generate("app_app_card_show", ['id' => $record->getCardId()->getId()]));
        $breadcrumbs->addItem('_');

        if($form->isValid()) {

            $data = $form->getData();

            if($data['attachments']) {
                $attachment = new Attachment();
                $attachment->setFile($data['attachments']);
                $attachment->setRecord($record);
                $attachment->upload();
                $record->addAttachment($attachment);
                $em->persist($attachment);
            }

            $record->setCategory($data['category']);
            $record->setSpeciality($data['speciality']);
            $record->setText($data['text']);
            $record->setLasteditor($this->getUser());

            $em->flush();

            return $this->redirectToRoute('app_app_record_show', array('id' => $record->getId()));
        }

        return $this->render("AppBundle:Record:edit.html.twig", array(
            'record' => $record,
            'form' => $form->createView(),
            'info' => ''
        ));
    }

    /**
     * @Route("/{id}/delete", requirements={ "id": "\d+" })
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);

        if(!$record) {
            throw $this->createNotFoundException(self::NOT_FOUND . $id);
        }

        $em->remove($record);
        $em->flush();

        return $this->redirectToRoute('app_app_patient_show', ['id' => $record->getPatientId()->getId()]);
    }

}