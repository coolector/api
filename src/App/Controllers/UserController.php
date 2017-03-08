<?php

namespace App\Controllers;

use JasonGrimes\Paginator;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends \SimpleUser\UserController
{
    /**
     * {@inheritdoc}
     */
    public function viewAction(Application $app, Request $request, $id)
    {
        $user = $this->userManager->getUser($id);

        if (!$user) {
            throw new NotFoundHttpException('No user was found with that ID.');
        }

        if (!$user->isEnabled() && !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException('That user is disabled (pending email confirmation).');
        }

        return $app['twig']->render($this->getTemplate('view'), array(
            'layout_template' => $this->getTemplate('layout'),
            'user' => $user,
            'imageUrl' => $this->getGravatarUrl($user->getEmail()),
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function editAction(Application $app, Request $request, $id)
    {
        $errors = array();

        $user = $this->userManager->getUser($id);
        if (!$user) {
            throw new NotFoundHttpException('No user was found with that ID.');
        }

        $customFields = $this->editCustomFields ?: array();

        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            if ($request->request->has('username')) {
                $user->setUsername($request->request->get('username'));
            }
            if ($request->request->get('password')) {
                if ($request->request->get('password') != $request->request->get('confirm_password')) {
                    $errors['password'] = 'Passwords don\'t match.';
                } else if ($error = $this->userManager->validatePasswordStrength($user, $request->request->get('password'))) {
                    $errors['password'] = $error;
                } else {
                    $this->userManager->setUserPassword($user, $request->request->get('password'));
                }
            }
            if ($app['security.authorization_checker']->isGranted('ROLE_ADMIN') && $request->request->has('roles')) {
                $user->setRoles($request->request->get('roles'));
            }

            foreach (array_keys($customFields) as $customField) {
                if ($request->request->has($customField)) {
                    $user->setCustomField($customField, $request->request->get($customField));
                }
            }

            $errors += $this->userManager->validate($user);

            if (empty($errors)) {
                $this->userManager->update($user);
                $msg = 'Saved account information.' . ($request->request->get('password') ? ' Changed password.' : '');
                $app['session']->getFlashBag()->set('alert', $msg);
            }
        }

        return $app['twig']->render($this->getTemplate('edit'), array(
            'layout_template' => $this->getTemplate('layout'),
            'error' => implode("\n", $errors),
            'user' => $user,
            'available_roles' => array('ROLE_USER', 'ROLE_ADMIN'),
            'image_url' => $this->getGravatarUrl($user->getEmail()),
            'customFields' => $customFields,
            'isUsernameRequired' => $this->isUsernameRequired,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function listAction(Application $app, Request $request)
    {
        $order_by = $request->get('order_by') ?: 'name';
        $order_dir = $request->get('order_dir') == 'DESC' ? 'DESC' : 'ASC';
        $limit = (int)($request->get('limit') ?: 50);
        $page = (int)($request->get('page') ?: 1);
        $offset = ($page - 1) * $limit;

        $criteria = array();
        if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $criteria['isEnabled'] = true;
        }

        $users = $this->userManager->findBy($criteria, array(
            'limit' => array($offset, $limit),
            'order_by' => array($order_by, $order_dir),
        ));
        $numResults = $this->userManager->findCount($criteria);

        $paginator = new Paginator($numResults, $limit, $page,
            $app['url_generator']->generate('user.list') . '?page=(:num)&limit=' . $limit . '&order_by=' . $order_by . '&order_dir=' . $order_dir
        );

        foreach ($users as $user) {
            $user->imageUrl = $this->getGravatarUrl($user->getEmail(), 40);
        }

        return $app['twig']->render($this->getTemplate('list'), array(
            'layout_template' => $this->getTemplate('layout'),
            'users' => $users,
            'paginator' => $paginator,

            // The following variables are no longer used in the default template,
            // but are retained for backward compatibility.
            'numResults' => $paginator->getTotalItems(),
            'nextUrl' => $paginator->getNextUrl(),
            'prevUrl' => $paginator->getPrevUrl(),
            'firstResult' => $paginator->getCurrentPageFirstItem(),
            'lastResult' => $paginator->getCurrentPageLastItem(),
        ));
    }
}