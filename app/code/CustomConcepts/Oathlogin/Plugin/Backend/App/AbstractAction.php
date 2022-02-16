<?php

/**
 * CustomConcepts_Oathlogin extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Oathlogin
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */


namespace CustomConcepts\Oathlogin\Plugin\Backend\App;

use Magento\Backend\App\AbstractAction as Sb;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\Driver\File as File;
use Magento\Backend\Model\Auth\Session;
use Magento\Security\Model\Plugin\Auth as SecurityPlugin;

class AbstractAction {

    const CLIENT_ID = 'oathlogin/general/clientId';
    const ENABLE_GOOGLE_LOGIN = 'oathlogin/general/show_banner';
    const ENABLE_NEW_USER_CREATE = 'oathlogin/general/enableNewUser';
    const HOST_DOMAIN = 'oathlogin/general/hostDomain';
    const DEFAULT_ROLE = 'oathlogin/general/defaultRole';
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'oathlogin/general/templateId';
    const DEFAULT_NAME_SENDER = 'trans_email/ident_general/name';
    const DEFAULT_EMAIL_SENDER = 'trans_email/ident_general/email';

    /**
     *
     * @var type \Magento\Framework\App\View
     */
    protected $layout;

    /**
     *
     * @var type \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     *
     * @var type Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var type Magento\Framework\Filesystem\Driver\File 
     */
    protected $file;

    /**
     *
     * @var type \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     *
     * @var type \Magento\User\Model\User
     */
    protected $user;

    /**
     *
     * @var type \Magento\Framework\Stdlib\CookieManagerInterface 
     */
    protected $cookieManager;

    /**
     *
     * @var type \Magento\Backend\Model\Session\AdminConfig 
     */
    protected $sessionConfig;

    /**
     *
     * @var type \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadata;

    /**
     *
     * @var type Magento\Security\Model\Plugin\Auth
     */
    protected $securityPlugin;

    /**
     *
     * @var type \Magento\Security\Model\AdminSessionsManager
     */
    protected $adminSessionManager;
    
    /**
     *
     * @var type \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     *
     * @var type string
     */
    protected $temp_id;

    /**
     *
     * @var type \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     *
     * @var type \Magento\Framework\Mail\Template\TransportBuilder 
     */
    protected $transportBuilder;

    /**
     *
     * @var type \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * 
     * @param \Magento\Framework\App\View $layout
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param File $file
     * @param Session $session
     * @param \Magento\User\Model\User $user
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Backend\Model\Session\AdminConfig $sessionConfig
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata
     * @param SecurityPlugin $securityPlugin
     * @param \Magento\Security\Model\AdminSessionsManager $adminSessionManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
    \Magento\Framework\App\View $layout, \Magento\Framework\View\Result\PageFactory $pageFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, File $file, Session $session, \Magento\User\Model\User $user, \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, \Magento\Backend\Model\Session\AdminConfig $sessionConfig, \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadata, SecurityPlugin $securityPlugin, \Magento\Security\Model\AdminSessionsManager $adminSessionManager, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->layout = $layout;
        $this->scopeConfig = $scopeConfig;
        $this->file = $file;
        $this->session = $session;
        $this->user = $user;
        $this->cookieManager = $cookieManager;
        $this->sessionConfig = $sessionConfig;
        $this->cookieMetadata = $cookieMetadata;
        $this->securityPlugin = $securityPlugin;
        $this->adminSessionManager = $adminSessionManager;
        $this->eventManager = $eventManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
    }

    /**
     * 
     * @param \CustomConcepts\Oathlogin\Plugin\Backend\App\sb $subject
     * @param RequestInterface $request
     */
    public function beforeDispatch(sb $subject, RequestInterface $request) {

        if ($this->getConfigValue(self::ENABLE_GOOGLE_LOGIN)) {
            $oathId = $this->getConfigValue(self::CLIENT_ID);

            if ($request->getFullActionName() == 'adminhtml_auth_login') {
                $this->pageFactory->create()->getConfig()->setMetaData('google-signin-client_id', $oathId);
            }

            $isOAuthLogin = $request->getParam('dfe-google-login');
            $token = $request->getParam('id_token');

            if ($isOAuthLogin && !$this->session->isLoggedIn() && $token && empty($request->getParam('login'))) {

                $path = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$token";

                $response = $this->file->fileGetContents($path);

                if ($response) {
                    $response = json_decode($response, true);

                    $email = $response['email'];

                    if ($email && $oathId == $response['aud']) {

                        $user = $this->user->load($email, 'email');
                        if ($user->hasData()) {
                            $this->generateSession($user);
                        } else {
                            $this->createNewUserAndLogin($response);
                        }
                    } else {
                        $this->messageManager->addErrorMessage(__('Oauth Id Different'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('No response from google'));
                }
            }
        }
    }

    /**
     * 
     * @param type $response array
     * @return boolean
     */
    public function isCreateNewUser($response) {
        $result = FALSE;
        if ($this->getConfigValue(self::ENABLE_NEW_USER_CREATE)) {
            if ($response['hd'] == $this->getConfigValue(self::HOST_DOMAIN)) {
                $result = TRUE;
            } else {
                $this->messageManager->addErrorMessage(__('You are not authorized to access this site'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('You are not authorized to access this site'));
        }

        return $result;
    }

    /**
     * 
     * @param string $path
     * @return type mixed
     */
    public function getConfigValue($path) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @param type $user
     */
    public function generateSession($user) {
        $this->session->setUser($user);
        $this->session->processLogin();
        if ($this->session->isLoggedIn()) {
            $this->generateAdminCookie();
        }
    }

    public function generateAdminCookie() {
        $cookieManager = $this->cookieManager;
        $cookieValue = $this->session->getSessionId();
        if ($cookieValue) {
            $sessionConfig = $this->sessionConfig;
            $cookiePath = $sessionConfig->getCookiePath(); //str_replace('autologin.php', 'index.php', $sessionConfig->getCookiePath());
            $cookieMetadata = $this->cookieMetadata
                    ->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($sessionConfig->getCookieDomain())
                    ->setSecure($sessionConfig->getCookieSecure())
                    ->setHttpOnly($sessionConfig->getCookieHttpOnly());
            $cookieManager->deleteCookie($this->session->getName());
            $cookieManager->setPublicCookie($this->session->getName(), $cookieValue, $cookieMetadata);
            $adminSessionManager = $this->adminSessionManager;
            $adminSessionManager->processLogin();
            $adminSessionManager->processProlong();
        }
    }

    /**
     * 
     * @param type $response Array
     */
    public function createNewUserAndLogin($response) {

        if ($this->isCreateNewUser($response)) {
            $user = $this->user;
            $role_id = $this->getConfigValue(self::DEFAULT_ROLE);
            $data = [
                'username' => $response['given_name'],
                'firstname' => $response['given_name'],
                'lastname' => $response['family_name'],
                'email' => $response['email'],
                'password' => $this->randomPassword(),
                'is_active' => 1,
                'role_id' => $role_id
            ];

            $user->setData($data);
            $user->save();
            $this->sendMail($data);
            $this->generateSession($user);
        }
    }
    
    /**
     * 
     * @return type String
     */
    public function randomPassword() {

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 5; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $int = '1234567890'; // hack for error Your password must include both numeric and alphabetic characters
        $intLength = strlen($int) - 1;
        
        for ($i = 0; $i < 3; $i++) {
            $n = rand(0, $intLength);
            $pass[] = $int[$n];
        }
        
        return implode($pass); //turn the array into a string
    }

    public function sendMail($data) {

        $emailTemplateVariables['username'] = $data['username'];
        $emailTemplateVariables['firstname'] = $data['firstname'];
        $emailTemplateVariables['lastname'] = $data['lastname'];
        $emailTemplateVariables['email'] = $data['email'];
        $emailTemplateVariables['password'] = $data['password'];

        $senderInfo['name'] = $this->getConfigValue(self::DEFAULT_NAME_SENDER);
        $senderInfo['email'] = $this->getConfigValue(self::DEFAULT_EMAIL_SENDER);

        $receiverInfo['name'] = $data['username'];
        $receiverInfo['email'] = $data['email'];

        $this->processSendMail($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * 
     * @param type $emailTemplateVariables
     * @param type $senderInfo
     * @param type $receiverInfo
     */
    public function processSendMail($emailTemplateVariables, $senderInfo, $receiverInfo) {
        $this->temp_id = $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * 
     * @param type $emailTemplateVariables
     * @param type $senderInfo
     * @param type $receiverInfo
     * @return \CustomConcepts\Oathlogin\Plugin\Backend\App\AbstractAction
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo) {
        $template = $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_ADMINHTML, /* here you can defile area and
                              store of template for which you prepare it */
                            'store' => 0, //This functionality always work for admin so store is 0 by default
                        ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }

}
