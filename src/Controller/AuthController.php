<?php

namespace Ivo\ProtectPage\Controller;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\PageModel;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AuthController extends Controller
{

    protected $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function __invoke(ResponseEvent $event)
    {
        $this->framework->initialize();
        $request = $event->getRequest();
        $response = $event->getResponse();
        $objRequestedPage = $request->get('pageModel');
        if (!$objRequestedPage) {
            return;
        }
        $objPage = $this->findProtectedPage($objRequestedPage);
        if ($objPage && (strpos(Environment::get('requestUri'), "/preview.php/") !== 0)) {
            $validate = $this->validate($objPage);
            if ($validate !== true) {
                $objTemplate = new FrontendTemplate('fe_authenticate');
                $objTemplate->title = $objRequestedPage->title;
                $objTemplate->alias = $objRequestedPage->alias;
                if ($objPage->auth_logo) {
                    $objFile = FilesModel::findByUuid($objPage->auth_logo);
                    if ($objFile !== null) {
                        $objTemplate->logo = $objFile->path;
                    }
                }
                $objTemplate->description = $objRequestedPage->description;
                $objTemplate->robots = $objRequestedPage->robots;
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
        if ($objPage->authRequired == "1") {
            return $objPage;
        } elseif ($objPage->pid != 0) {
            $objParent = PageModel::findByPk($objPage->pid);
            return $this->findProtectedPage($objParent);
        } else {
            return false;
        }
    }
}