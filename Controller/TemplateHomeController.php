<?php

namespace FormaLibre\ClarolineComThemeBundle\Controller;

use Claroline\CoreBundle\Entity\Content;
use Claroline\CoreBundle\Persistence\ObjectManager;
use FormaLibre\ClarolineComThemeBundle\Form\ContentType;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TemplateHomeController extends Controller
{
    private $authorization;
    private $formFactory;
    private $om;
    private $request;
    private $router;

    /**
     * @DI\InjectParams({
     *     "authorization" = @DI\Inject("security.authorization_checker"),
     *     "formFactory"   = @DI\Inject("form.factory"),
     *     "om"            = @DI\Inject("claroline.persistence.object_manager"),
     *     "requestStack"  = @DI\Inject("request_stack"),
     *     "router"        = @DI\Inject("router")
     * })
     */
    public function __construct(
        AuthorizationCheckerInterface $authorization,
        FormFactory $formFactory,
        ObjectManager $om,
        RequestStack $requestStack,
        RouterInterface $router
    )
    {
        $this->authorization = $authorization;
        $this->formFactory = $formFactory;
        $this->om = $om;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
    }

    /**
     * @EXT\Route(
     *     "/template/{type}/text/{content}",
     *     name="formalibre_claroline_com_theme_template_text",
     *     options={"expose"=true}
     * )
     * @EXT\Template()
     */
    public function templateTextAction($type, Content $content)
    {
        $canEdit = $this->canEditHome();

        return array('type' => $type, 'content' => $content, 'canEdit' => $canEdit);
    }

    /**
     * @EXT\Route(
     *     "/template/{type}/text/{content}/edit/form",
     *     name="formalibre_claroline_com_theme_template_text_edit_form",
     *     options={"expose"=true}
     * )
     * @EXT\Template()
     */
    public function contentEditFormAction($type, Content $content)
    {
        $this->checkHomeEdition();
        $form = $this->formFactory->create(new ContentType(), $content);
        
        return array(
            'form' => $form->createView(),
            'type' => $type,
            'content' => $content
        );
    }

    /**
     * @EXT\Route(
     *     "/template/{type}/text/{content}/edit",
     *     name="formalibre_claroline_com_theme_template_text_edit",
     *     options={"expose"=true}
     * )
     * @EXT\Template("FormaLibreClarolineComThemeBundle:TemplateHome:contentEditForm.html.twig")
     */
    public function contentEditAction($type, Content $content)
    {
        $this->checkHomeEdition();
        $form = $this->formFactory->create(new ContentType(), $content);
        $form->handleRequest($this->request);

        if ($form->isValid()) {
            $this->om->persist($content);
            $this->om->flush();

            $route = $this->router->generate(
                'claro_get_content_by_type',
                array('type' => $type)
            );

            return new RedirectResponse($route);
        } else {

            return array(
                'form' => $form->createView(),
                'type' => $type,
                'content' => $content
            );
        }
    }

    private function canEditHome()
    {
        return $this->authorization->isGranted('ROLE_ADMIN') || 
            $this->authorization->isGranted('ROLE_HOME_MANAGER');
    }

    private function checkHomeEdition()
    {
        if (!$this->canEditHome()) {

            throw new AccessDeniedException();
        }
    }
}
