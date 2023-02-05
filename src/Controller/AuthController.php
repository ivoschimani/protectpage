<?php

namespace Ivo\ProtectPage\Controller;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;

class AuthController extends Controller
{

    protected $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function __invoke(FilterResponseEvent $event)
    {
        $this->framework->initialize();
        $request = $event->getRequest();
        $response = $event->getResponse();
        $objPage = $request->get('pageModel');
        if (!$objPage) {
            return;
        }
        if ($objPage = $this->findProtectedPage($objPage)) {
            if (($validate = $this->validate($objPage)) !== true) {
                $objTemplate = new \FrontendTemplate('fe_authenticate');
                $objTemplate->title = $objPage->title;
                $objTemplate->alias = $objPage->alias;
                if ($objPage->auth_logo) {
                    $objFile = \FilesModel::findByUuid($objPage->auth_logo);
                    if ($objFile !== null) {
                        $objTemplate->logo = $objFile->path;
                    }
                }
                $objTemplate->description = $objPage->description;
                $objTemplate->robots = $objPage->robots;
                $objTemplate->charset = Config::get('characterSet');
                $objTemplate->base = Environment::get('base');
                if ($validate == 'wrong_data') {
                    $objTemplate->error = true;
                }
                $response->setContent(FrontendTemplate::replaceDynamicScriptTags(FrontendTemplate::replaceInsertTags($objTemplate->parse())));
                return $response->send();
                exit;
            }
        }
    }

    protected function validate($objPage)
    {
        $user = Input::post('username');
        $pw = Input::post('password');
        $this->checkAuth($objPage);
        $hash = \md5($objPage->id . $objPage->auth_pw . strtotime('today'));
        if (($user == $objPage->auth_user && \password_verify($pw, $objPage->auth_pw)) || $_COOKIE[$hash]) {
            \setcookie($hash, 1, time() + 86400);
            return true;
        } elseif (Input::post('username') != '' && Input::post('passwort') != '') {
            return 'wrong_data';
        } else {
            return false;
        }
    }

    protected function findProtectedPage($objPage)
    {
        if ($objPage->authRequired) {
            return $objPage;
        } elseif ($objPage->pid != 0) {
            $objParent = \PageModel::findByPk($objPage->pid);
            return $this->findProtectedPage($objParent);
        } else {
            return false;
        }
    }
}