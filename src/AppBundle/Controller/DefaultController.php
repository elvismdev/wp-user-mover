<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Parenthesis\WPBundle\Entity\User;
use Parenthesis\WPBundle\Entity\UserMeta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $offset = $request->request->getInt('offset', 0);
        $limit = $request->request->getInt('limit', 5);

        $em = $this->get('doctrine')->getManager('source');
        $userManager = $this->get('parenthesis.wp.manager.user');
        $metaManager = $this->get('parenthesis.wp.manager.user_meta');

        $users = $em->getRepository('AppBundle:WpUsers')->findBy(array(), null, $limit, $offset);

        foreach ($users as $user) {
            if ($user->getUserEmail() != '' && !$userManager->findOneBy(array('email' => $user->getUserEmail()))) {
                $u =  new User();
                $u->setLogin($user->getUserLogin());
                $u->setPass($user->getUserPass());
                $u->setNicename($user->getUserNicename());
                $u->setEmail($user->getUserEmail());
                $u->setUrl($user->getUserUrl());
                $u->setRegistered(new \DateTime());
                $u->setActivationKey($user->getUserActivationKey());
                $u->setStatus($user->getUserStatus());
                $u->setDisplayName($user->getDisplayName());

                $userManager->save($u);

                if ($u->getId()) {
                    $metas = $em->getRepository('AppBundle:WpUsermeta')->findByUserId($user->getId());

                    foreach ($metas as $meta) {
                        $m = new UserMeta();
                        $m->setKey($meta->getMetaKey());
                        $m->setValue($meta->getMetaValue());
                        $m->setUser($u);

                        $metaManager->save($m);
                    }
                }
            }
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
