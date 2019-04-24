<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Transaction;
use AppBundle\Repository\TransactionRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Transaction controller.
 *
 * @Route("/")
 */
class TransactionController extends AbstractFOSRestController
{
    /**
     * screen 2.
     *
     * @Route("/screen2", name="_index2")
     * @Method("GET")
     */
    public function screen2Action()
    {
        return $this->render('transaction/screen2.html.twig');
    }

    /**
     * Return all transaction infos filtered by specific month of current year.
     *
     * @param Request $request
     * @param $month
     * @Rest\Get("/api/transactions",name="api_index")
     *
     * @return Response
     */
    public function getTransactionsData(Request $request, PaginatorInterface $paginator, $month = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $month) {
            $month = $request->get('month', date('m'));
        }
        /** @var TransactionRepository $transactionRepo */
        $transactionRepo = $em->getRepository('AppBundle:Transaction');
        $infos['transactions'] = $transactionRepo->getFiltredTransactionByMonth($month);
        $infos['TreasuryInfos'] = $transactionRepo->getTreasuryInfos($month);
        $infos['transactions'] = $paginator->paginate($infos['transactions'],
            $request->get('page', 1),
            $request->get('limit', 5)
        );

        $view = $this->view($infos, 200)
            ->setTemplate('transaction/screen2Content.html.twig')
            ->setTemplateVar('transaction');

        return $this->handleView($view);
    }
}
