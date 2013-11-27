<?php

namespace CM\CMBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Doctrine\ORM\EntityManager;
use Cm\CMBundle\Entity\EntityUser;
use Cm\CMBundle\Entity\Image;
use Cm\CMBundle\Entity\Post;

class UserAuthentication
{
    private $securityContext;

    private $session;

    private $em;

    private $helper;

    public function __construct(SecurityContext $securityContext, Session $session, EntityManager $em, Helper $helper)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->em = $em;
        $this->helper = $helper;
    }

    public function isAuthenticated($role = 'ROLE_USER')
    {
        if ($this->securityContext->isGranted($role) && is_null($this->session->get('user/username'))) {
            $this->updateProfile();
        } elseif (!$this->securityContext->isGranted('ROLE_USER') && !is_null($this->session->get('user/username'))) {
            $this->session->remove('user');
        }
    
        return $this->securityContext->isGranted($role);
    }  
  
    /**
     * updateProfile function.
     * 
     * @access public
     * @return void
     */
    public function updateProfile()
    {            
        $user = $this->securityContext->getToken()->getUser();
        $groups = $this->em->getRepository('CMBundle:User')->getAdminGroupsIds($user->getId());
        // $groups = GroupUserQuery::create()->filterByUserId($user->getId())->filterByAdmin(1)->select(array('GroupId'))->setFormatter('PropelSimpleArrayFormatter')->find()->toArray();
        $pages = $this->em->getRepository('CMBundle:User')->getAdminPagesIds($user->getId());
        // $pages = PageUserQuery::create()->filterByUserId($user->getId())->filterByAdmin(1)->select(array('PageId'))->setFormatter('PropelSimpleArrayFormatter')->find()->toArray();
        // $siti = SitiQuery::create()->filterByUserId($user->getId())->select(array('Id'))->setFormatter('PropelSimpleArrayFormatter')->find()->toArray();

        $this->session->set('user/id', $user->getId());
        $this->session->set('user/username', $user->getSlug());
        $this->session->set('user/first_name', $user->getFirstName());
        $this->session->set('user/last_name', $user->getLastName());
        $this->session->set('user/full_name', (string)$user);
        $this->session->set('user/img', $user->getImg());
        $this->session->set('user/img_offset', $user->getImgOffset());
        $this->session->set('user/groups_admin', $groups);
        $this->session->set('user/pages_admin', $pages);
        // $this->session->set('siti_admin', $siti, 'user');
        // $this->session->set('languages', explode(', ', $user->getSiti()->getLingue()), 'user');
          
        $this->updateProfileComplete();                                               
    }
  
    /**
     * updateProfileComplete function.
     * 
     * @access public
     * @return void
     */
    public function updateProfileComplete()
    {            
        $user = $this->securityContext->getToken()->getUser();
        // $biography = BiographyQuery::getUserBiography($user->getId()); // TODO: fix                                                                                                                             

        if (($user->getBirthDate() || $user->getCityBirth() || $user->getCityCurrent() || $user->getSex()) && $user->getImg() /*&& $biography */&& $user->getUserUserTags()->count() >= 1) {
            $this->session->set('user/profile_complete', true);
        } else {  
            $this->session->set('user/profile_complete', false); 
        }                                                         
    }
    
    /**
     * isProfileComplete function.
     * 
     * @access public
     * @return void
     */
    public function isProfileComplete()
    {
        if (!$this->session->has('user/profile_complete')) {
            $this->updateProfileComplete();
        }
        return $this->session->get('user/profile_complete');
    }

    public function isAdminOf($object)
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        switch ($this->helper->className($object)) {
            case 'Group':
                return in_array($object->getId(), (array)$this->session->get('user/groups_admin'));
                break;
            case 'Page':
                return in_array($object->getId(), (array)$this->session->get('user/pages_admin'));
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * canManage function.
     * 
     * @access public
     * @static
     * @param mixed $object
     * @return void
     */
    public function canManage($object)
    {
        // 1) AUTHENTICATION
        if ($this->isAuthenticated()) {

            // 2) ADMIN
            if ($this->isAuthenticated('ROLE_SUPER_ADMIN')) {
                return true;
            }
                  
            $user_id = $this->securityContext->getToken()->getUser()->getId();
            if (method_exists($object, 'getCreator')) {
                $creator_id = $object->getCreator()->getId();
            } elseif (method_exists($object, 'getUser'))
            {
                $creator_id = $object->getUser()->getId();
            } elseif (method_exists($object, 'getPost')) {
                if (!is_null($object->getPost())) {
                    $creator_id = $object->getPost()->getCreator()->getId();
                } else {
                    return true; // is a newly created entity!
                }
            } else {
                throw new \BadMethodCallException('Neither \'getCreator\' nor \'getUser\' methods exsist in class '.get_class($object));
            }
            
            // 3) CREATOR
            if ($user_id == $creator_id) {
                return true;
            }  
    
            if ($object instanceof Post || $object instanceof Image) {
                $group_id = $object->getGroup()->getId();  
                $page_id = $object->getPage()->getId();   
            } elseif ($object instanceof Groups) {
                $group_id = $object->getId();        
            } elseif ($object instanceof Pages) {
                $page_id = $object->getId();        
            }
                  
            // 4) GROUP
            if (!empty($group_id) && in_array($group_id, $this->session->get('user/groups_admin'))) {
                return true;
            }      
                  
            // 5) PAGE 
            if (!empty($page_id) && in_array($page_id, $this->session->get('user/pages_admin'))) {
                return true;
            }  
    
            // 6) PROTAGONISTS
            if (method_exists($object, 'getEntityUsers') && !$object->getEntityUsers()->isEmpty()) {
                $protagonists = $object->getEntityUsers();
                foreach ($protagonists as $protagonist) {
                    if ($protagonist->getUser()->getId() == $user_id && $protagonist->isAdmin() && $protagonist->getStatus() == EntityUser::STATUS_ACTIVE) {
                        return true;
                    }
                }
            }
        } else {  // Not authenticated
            return false;
        }
    }
}