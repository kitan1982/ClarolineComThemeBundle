<?php

namespace FormaLibre\ClarolineComThemeBundle\Controller;

use Claroline\CursusBundle\Manager\CursusApiManager;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RemoteCursusController extends Controller
{
    private $campusName;
    private $ch;
    private $cursusApiManager;

    /**
     * @DI\InjectParams({
     *     "ch"               = @DI\Inject("claroline.config.platform_config_handler"),
     *     "cursusApiManager" = @DI\Inject("claroline.manager.cursus_api_manager")
     * })
     */
    public function __construct($ch, CursusApiManager $cursusApiManager)
    {
        $this->campusName = $ch->getParameter('campusName');
        $this->ch = $ch;
        $this->cursusApiManager = $cursusApiManager;
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
        $levelMax = array();
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

                if (!isset($levelMax[$root])) {
                    $levelMax[$root] = 0;
                }
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
                }

                if ((isset($levelMax[$root]) && ($lvl > $levelMax[$root])) ||
                    !isset($levelMax[$root])) {

                    $levelMax[$root] = $lvl;
                }
            }
        }

        return array(
            'roots' => $roots,
            'cursusChildren' => $cursusChildren,
            'levelMax' => $levelMax
        );
    }
}
