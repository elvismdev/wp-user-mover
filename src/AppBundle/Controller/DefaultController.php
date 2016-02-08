<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WpGroupsUserGroup;
use AppBundle\Entity\WpWumCount;
use AppBundle\Entity\WpWumLogs;
use Doctrine\ORM\EntityManager;
use Parenthesis\WPBundle\Entity\User;
use Parenthesis\WPBundle\Entity\UserMeta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Count;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return array()
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @param Request $request
     * @param int $group_s
     * @param int $recordsQty
     * @return JsonResponse
     *
     * @Route("/moveUsers/{group_s}/{recordsQty}", defaults={"recordsQty": 100}, name="homepage")
     */
    public function moveUsersAction(Request $request, $group_s, $recordsQty)
    {
        // GET/POST
        $offset = $request->request->getInt('offset', 0);

        $moved = $exist = $ignored = $errors = 0;
        $limit = $offset + $recordsQty;

        $em_s = $this->get('doctrine')->getManager('source');
        $em_d = $this->get('doctrine')->getManager('destiny');

        $group_s = $em_d->getRepository('AppBundle:WpGroupsGroup')->find($group_s);
        if (!$group_s) {
            return JsonResponse::create(array(
                'errors' => 'Group does not exist'
            ));
        }

        // Clear audit counters
        $this->Count($em_s, 'clear');

        $users_s = $em_s->getRepository('AppBundle:WpUsers')->findBy(array(), null, $limit, $offset);
        foreach ($users_s as $user_s) {
            if (!$user_s->getUserEmail()) {
                $this->Count($em_s, 'ignored', $ignored);
                continue;
            }

            $userManager = $this->get('parenthesis.wp.manager.user');
            $metaManager = $this->get('parenthesis.wp.manager.user_meta');

            $user_d = $userManager->findOneBy(array('email' => $user_s->getUserEmail()));
            if (!$user_d) {
                $user_d =  new User();

                try {
                    $user_d->setLogin($user_s->getUserLogin());
                    $user_d->setPass($user_s->getUserPass());
                    $user_d->setNicename($user_s->getUserNicename());
                    $user_d->setEmail($user_s->getUserEmail());
                    $user_d->setUrl($user_s->getUserUrl());
                    $user_d->setRegistered(new \DateTime());
                    $user_d->setActivationKey($user_s->getUserActivationKey());
                    $user_d->setStatus($user_s->getUserStatus());
                    $user_d->setDisplayName($user_s->getDisplayName());

                    $userManager->save($user_d);
                } catch (\Exception $e) {
                    $this->Log($em_s, 'ERR_USER_SAVE', $e->getMessage(), $user_s->getUserEmail(), $errors);
                    continue;
                }

                $this->Count($em_s, 'moved', $moved);
            } else {
                $this->Count($em_s, 'exist', $exist);
            }

            // Save user's meta
            $metas_s = $em_s->getRepository('AppBundle:WpUsermeta')->findByUserId($user_s->getId());
            foreach ($metas_s as $meta_s) {
                $meta_d = new UserMeta();

                try {
                    $meta_d->setKey($meta_s->getMetaKey());
                    $meta_d->setValue($meta_s->getMetaValue());
                    $meta_d->setUser($user_d);

                    $metaManager->save($meta_d);
                } catch (\Exception $e) {
                    $this->Log($em_s, 'ERR_USER_META_SAVE', $e->getMessage(), $user_s->getUserEmail(), $errors);
                }
            }

            // Save user's group
            if (!$em_d->getRepository('AppBundle:WpGroupsUserGroup')->findOneBy(array('userId' => $user_d->getId(), 'groupId' => $group_s->getGroupId()))) {
                $group_d = new WpGroupsUserGroup();

                try {
                    $group_d->setUserId($user_d->getId());
                    $group_d->setGroupId($group_s->getGroupId());

                    $em_d->persist($group_d);
                    $em_d->flush();
                } catch (\Exception $e) {
                    $this->Log($em_s, 'ERR_USER_GROUP_SAVE', $e->getMessage(), $user_s->getUserEmail(), $errors);
                }
            }
        }

        $response = array(
            'moved' => $moved,
            'exist' => $exist,
            'ignored' => $ignored,
            'errors' => $errors
        );

        return JsonResponse::create($response);
    }

    /**
     * @param EntityManager $em
     * @param string $description
     * @param string $exception
     * @param string $email
     * @param string $type
     * @param string $errors
     */
    private function Log(EntityManager $em, $description, $exception, $email, $type = 'success', &$errors)
    {
        if (strstr($description, 'ERR')) {
            $type = 'error';
        }

        $log = new WpWumLogs($description, $exception, $email, $type);
        $em->persist($log);
        $em->flush($log);

        $this->Count($em, 'errors', $errors);
    }

    /**
     * @param EntityManager $em
     * @param $type
     * @param $var
     */
    private function Count(EntityManager $em, $type, &$var = null)
    {
        $var++;

        // Always first row
        $count = $em->getRepository('AppBundle:WpWumCount')->findBy(array(), null, 1, 0);
        if (!$count = array_shift(array_values($count))) {
            $count = new WpWumCount();
        }

        switch ($type) {
            case 'moved':
                $count->setMoved();
                break;
            case 'exist':
                $count->setExist();
                break;
            case 'ignored':
                $count->setIgnored();
                break;
            case 'errors':
                $count->setError();
                break;
            case 'clear':
                $count->setClear();
                break;
        }

        $em->persist($count);
        $em->flush();
    }
}
