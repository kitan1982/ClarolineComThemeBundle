<?php

namespace FormaLibre\ClarolineComThemeBundle\Controller;

use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Manager\OauthManager;
use Claroline\CursusBundle\Manager\CursusApiManager;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemoteCursusController extends Controller
{
    private $campusName;
    private $ch;
    private $cursusApiManager;
    private $oauthManager;

    /**
     * @DI\InjectParams({
     *     "ch"               = @DI\Inject("claroline.config.platform_config_handler"),
     *     "cursusApiManager" = @DI\Inject("claroline.manager.cursus_api_manager"),
     *     "oauthManager"     = @DI\Inject("claroline.manager.oauth_manager")
     * })
     */
    public function __construct(
        $ch,
        CursusApiManager $cursusApiManager,
        OauthManager $oauthManager
    )
    {
        $this->campusName = $ch->getParameter('campusName');
        $this->ch = $ch;
        $this->cursusApiManager = $cursusApiManager;
        $this->oauthManager = $oauthManager;
    }

    /**
     * @EXT\Route(
     *     "/cursus/list",
     *     name="formalibre_claroline_com_theme_cursus_list",
     *     options={"expose"=true}
     * )
     * @EXT\Template()
     */
    public function cursusListAction()
    {
        $roots = array();
        $cursusChildren = array();
        $campus = $this->oauthManager->findFriendRequestByName($this->campusName);
        $allCursus = $this->cursusApiManager->getRemoteCursus($this->campusName);

        foreach ($allCursus as $cursus) {
            $root = $cursus['root'];
            $lvl = $cursus['lvl'];
            $id = $cursus['id'];

            if ($lvl === 0) {
                $roots[$id] = array(
                    'id' => $id,
                    'code' => isset($cursus['code']) ? $cursus['code'] : null,
                    'description' => isset($cursus['description']) ? $cursus['description'] : null,
                    'title' => $cursus['title'],
                    'blocking' => $cursus['blocking'],
                    'cursus_order' => $cursus['cursusOrder'],
                    'root' => $root,
                    'lvl' => $lvl,
                    'lft' => $cursus['lft'],
                    'rgt' => $cursus['rgt'],
                    'details' => $cursus['details']
                );
            } else {
                $parentId = $cursus['parentId'];

                if (!isset($cursusChildren[$parentId])) {
                    $cursusChildren[$parentId] = array();
                }
                $cursusChildren[$parentId][$id] = array(
                    'id' => $id,
                    'code' => isset($cursus['code']) ? $cursus['code'] : null,
                    'description' => isset($cursus['description']) ? $cursus['description'] : null,
                    'title' => $cursus['title'],
                    'blocking' => $cursus['blocking'],
                    'cursus_order' => $cursus['cursusOrder'],
                    'root' => $root,
                    'lvl' => $lvl,
                    'lft' => $cursus['lft'],
                    'rgt' => $cursus['rgt'],
                    'details' => $cursus['details']
                );

                if (isset($cursus['course'])) {
                    $cursusChildren[$parentId][$id]['course'] = $cursus['course'];

                    if (!is_null($campus) &&
                        isset($cursus['course']['icon']) &&
                        !empty($cursus['course']['icon'])) {

                        $hostName = preg_replace('/\/(web\/)?(app_dev|app)\.php(\/)?$/', '', $campus->getHost());
                        $cursusChildren[$parentId][$id]['course']['icon'] =
                            $hostName . '/web/files/cursusbundle/icons/' . $cursus['course']['icon'];
                    }
                }
            }
        }

        return array('roots' => $roots, 'cursusChildren' => $cursusChildren);
    }

    /**
     * @EXT\Route(
     *     "/cursus/{cursusId}/hierarchy/register",
     *     name="formalibre_claroline_com_theme_cursus_hierarchy_register",
     *     options={"expose"=true}
     * )
     * @EXT\ParamConverter("authenticatedUser", options={"authenticatedUser" = true})
     */
    public function cursusHierarchyRegisterAction(User $authenticatedUser, $cursusId)
    {
        $datas = $this->cursusApiManager->registerUserToCursusHierarchy(
            $this->campusName,
            $authenticatedUser,
            $cursusId
        );

        return new JsonResponse($datas, 200);
    }


    /**
     * @EXT\Route(
     *     "/elearning/catalog",
     *     name="formalibre_claroline_com_theme_elearning_catalog",
     *     options={"expose"=true}
     * )
     * @EXT\Template()
     */
    public function eLearningCatalogAction()
    {
        $courses = $this->cursusApiManager->getRemoteCourses($this->campusName);
        $sortedCourses = array();

        foreach ($courses as $course) {
            $code = $course['code'];

            switch ($code) {
                case 'blended_learning':
                    $sortedCourses['blended_learning'] = $course;
                    break;
                case 'blog':
                    $sortedCourses['blog'] = $course;
                    break;
                case 'account':
                    $sortedCourses['account'] = $course;
                    break;
                case 'ws_rights':
                    $sortedCourses['ws_rights'] = $course;
                    break;
                case 'resources_rights':
                    $sortedCourses['resources_rights'] = $course;
                    break;
                case 'environment':
                    $sortedCourses['environment'] = $course;
                    break;
                case 'teams_badges':
                    $sortedCourses['teams_badges'] = $course;
                    break;
                case 'ws_teams_badges':
                    $sortedCourses['ws_teams_badges'] = $course;
                    break;
                case 'resources_teams_badges':
                    $sortedCourses['resources_teams_badges'] = $course;
                    break;
                case 'ws_config':
                    $sortedCourses['ws_config'] = $course;
                    break;
                case 'ws_roles_users_management':
                    $sortedCourses['ws_roles_users_management'] = $course;
                    break;
                case 'ws_rights_management':
                    $sortedCourses['ws_rights_management'] = $course;
                    break;
                case 'personal_ws':
                    $sortedCourses['personal_ws'] = $course;
                    break;
                case 'online_evaluation':
                    $sortedCourses['online_evaluation'] = $course;
                    break;
                case 'exercises':
                    $sortedCourses['exercises'] = $course;
                    break;
                case 'forum':
                    $sortedCourses['forum'] = $course;
                    break;
                case 'social_medias':
                    $sortedCourses['social_medias'] = $course;
                    break;
                case 'tools':
                    $sortedCourses['tools'] = $course;
                    break;
                case 'home_page':
                    $sortedCourses['home_page'] = $course;
                    break;
                case 'platform_config':
                    $sortedCourses['platform_config'] = $course;
                    break;
                case 'platform_roles_users_management':
                    $sortedCourses['platform_roles_users_management'] = $course;
                    break;
                case 'screenwriting':
                    $sortedCourses['screenwriting'] = $course;
                    break;
                case 'portfolio':
                    $sortedCourses['portfolio'] = $course;
                    break;
                case 'resources_arrangement':
                    $sortedCourses['resources_arrangement'] = $course;
                    break;
                case 'platform_roles':
                    $sortedCourses['platform_roles'] = $course;
                    break;
                case 'ws_roles':
                    $sortedCourses['ws_roles'] = $course;
                    break;
                case 'website':
                    $sortedCourses['website'] = $course;
                    break;
                case 'survey':
                    $sortedCourses['survey'] = $course;
                    break;
                case 'tutoring':
                    $sortedCourses['tutoring'] = $course;
                    break;
                case 'platform_users':
                    $sortedCourses['platform_users'] = $course;
                    break;
                case 'ws_users':
                    $sortedCourses['ws_users'] = $course;
                    break;
                case 'wiki':
                    $sortedCourses['wiki'] = $course;
                    break;
            }
        }

        return array('courses' => $courses, 'sortedCourses' => $sortedCourses);
    }
}
