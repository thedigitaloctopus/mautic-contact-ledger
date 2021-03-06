<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactLedgerBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Controller\AjaxLookupControllerTrait;
use Mautic\CoreBundle\Helper\UTF8Helper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    use AjaxLookupControllerTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function globalRevenueAction(Request $request)
    {
        $params    = $this->getDateParams();
        $cache_dir =$this->container->getParameter('kernel.cache_dir');

        // Get the API payload to test.
        //$params['limit'] = 1000; // just in case we want to set this, or use a config parameter

        $em     = $this->dispatcher->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo   = $em->getRepository(\MauticPlugin\MauticContactLedgerBundle\Entity\CampaignSourceStats::class);

        $data       = $repo->getDashboardRevenueWidgetData($params, false, $cache_dir);

        $headers    = [
            'mautic.contactledger.dashboard.revenue.header.active',
            'mautic.contactledger.dashboard.revenue.header.id',
            'mautic.contactledger.dashboard.revenue.header.name',
            'mautic.contactledger.dashboard.revenue.header.received',
            'mautic.contactledger.dashboard.revenue.header.scrubbed',
            'mautic.contactledger.dashboard.revenue.header.declined',
            'mautic.contactledger.dashboard.revenue.header.converted',
            'mautic.contactledger.dashboard.revenue.header.revenue',
            'mautic.contactledger.dashboard.revenue.header.cost',
            'mautic.contactledger.dashboard.revenue.header.gm',
            'mautic.contactledger.dashboard.revenue.header.margin',
            'mautic.contactledger.dashboard.revenue.header.ecpm',
        ];
        foreach ($headers as $header) {
            $data['columns'][] = [
                'title' => $this->translator->trans($header),
            ];
        }
        $data = UTF8Helper::fixUTF8($data);

        return $this->sendJsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function sourceRevenueAction(Request $request)
    {
        $params    = $this->getDateParams();
        $cache_dir = $this->container->getParameter('kernel.cache_dir');

        // Get the API payload to test.
        //$params['limit'] = 1000; // just in case we want to set this, or use a config parameter
        $em      = $this->dispatcher->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo    = $em->getRepository(\MauticPlugin\MauticContactLedgerBundle\Entity\CampaignSourceStats::class);
        $groupBy = $request->request->get('groupby', 'Source Name');

        $data       = $repo->getDashboardRevenueWidgetData($params, true, $cache_dir, $groupBy);

        $headers    = [
            'mautic.contactledger.dashboard.source-revenue.header.active',
            'mautic.contactledger.dashboard.source-revenue.header.id',
            'mautic.contactledger.dashboard.source-revenue.header.name',
            ];
        if ('Source Category' == $groupBy) {
            $headers[] = 'mautic.contactledger.dashboard.source-revenue.header.category';
        } else { // groupBy = Source Name
            $headers[] = 'mautic.contactledger.dashboard.source-revenue.header.sourceid';
            $headers[] = 'mautic.contactledger.dashboard.source-revenue.header.sourcename';
            $headers[] = 'mautic.contactledger.dashboard.source-revenue.header.utmsource';
        }

        $headers = array_merge($headers, [
            'mautic.contactledger.dashboard.source-revenue.header.received',
            'mautic.contactledger.dashboard.source-revenue.header.scrubbed',
            'mautic.contactledger.dashboard.source-revenue.header.declined',
            'mautic.contactledger.dashboard.source-revenue.header.converted',
            'mautic.contactledger.dashboard.source-revenue.header.revenue',
            'mautic.contactledger.dashboard.source-revenue.header.cost',
            'mautic.contactledger.dashboard.source-revenue.header.gm',
            'mautic.contactledger.dashboard.source-revenue.header.margin',
            'mautic.contactledger.dashboard.source-revenue.header.ecpm',
        ]);
        foreach ($headers as $header) {
            $data['columns'][] = [
                'title' => $this->translator->trans($header),
            ];
        }
        $data = UTF8Helper::fixUTF8($data);

        return $this->sendJsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function clientRevenueAction(Request $request)
    {
        $params    = $this->getDateParams();
        $cache_dir = $this->container->getParameter('kernel.cache_dir');

        // Get the API payload to test.
        //$params['limit'] = 1000; // just in case we want to set this, or use a config parameter
        $em      = $this->dispatcher->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo    = $em->getRepository(\MauticPlugin\MauticContactLedgerBundle\Entity\CampaignClientStats::class);
        $groupBy = $request->request->get('groupby', 'Client Name');

        $data       = $repo->getDashboardClientWidgetData($params, $cache_dir, $groupBy);

        $headers    = [
            'mautic.contactledger.dashboard.client-revenue.header.active',
            'mautic.contactledger.dashboard.client-revenue.header.id',
            'mautic.contactledger.dashboard.client-revenue.header.name',
        ];
        if ('Client Category' == $groupBy) {
            $headers[] = 'mautic.contactledger.dashboard.client-revenue.header.category';
        } else { // groupBy = Source Name
            $headers[] = 'mautic.contactledger.dashboard.client-revenue.header.clientid';
            $headers[] = 'mautic.contactledger.dashboard.client-revenue.header.clientname';
            $headers[] = 'mautic.contactledger.dashboard.client-revenue.header.utmsource';
        }

        $headers = array_merge($headers, [
            'mautic.contactledger.dashboard.client-revenue.header.received',
            'mautic.contactledger.dashboard.client-revenue.header.declined',
            'mautic.contactledger.dashboard.client-revenue.header.converted',
            'mautic.contactledger.dashboard.client-revenue.header.revenue',
            // hide the nexxt 3 columns until cost is processed correctly
            // 'mautic.contactledger.dashboard.client-revenue.header.cost',
            // 'mautic.contactledger.dashboard.client-revenue.header.gm',
            // 'mautic.contactledger.dashboard.client-revenue.header.margin',
            'mautic.contactledger.dashboard.client-revenue.header.ecpm',
            'mautic.contactledger.dashboard.client-revenue.header.rpu',
        ]);
        foreach ($headers as $header) {
            $data['columns'][] = [
                'title' => $this->translator->trans($header),
            ];
        }
        $data = UTF8Helper::fixUTF8($data);

        return $this->sendJsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function clientStatsTabAction(Request $request)
    {
        $params               = $this->getDateParams();
        $params['campaignId'] = $request->request->get('campaignId');
        $cache_dir            = $this->container->getParameter('kernel.cache_dir');

        // Get the API payload to test.
        //$params['limit'] = 1000; // just in case we want to set this, or use a config parameter
        $em      = $this->dispatcher->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo    = $em->getRepository(\MauticPlugin\MauticContactLedgerBundle\Entity\CampaignClientStats::class);

        $data       = $repo->getCampaignClientTabData($params, $cache_dir);

        $headers    = [
            'mautic.contactledger.dashboard.client-revenue.header.clientid',
            'mautic.contactledger.dashboard.client-revenue.header.clientname',
            'mautic.contactledger.dashboard.client-revenue.header.utmsource',
            'mautic.contactledger.dashboard.client-revenue.header.received',
            'mautic.contactledger.dashboard.client-revenue.header.declined',
            'mautic.contactledger.dashboard.client-revenue.header.converted',
            'mautic.contactledger.dashboard.client-revenue.header.revenue',
            'mautic.contactledger.dashboard.client-revenue.header.ecpm',
            'mautic.contactledger.dashboard.client-revenue.header.rpu',
        ];
        foreach ($headers as $header) {
            $data['columns'][] = [
                'title' => $this->translator->trans($header),
            ];
        }
        $data = UTF8Helper::fixUTF8($data);

        return $this->sendJsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function sourceStatsTabAction(Request $request)
    {
        $params               = $this->getDateParams();
        $params['campaignId'] = $request->request->get('campaignId');
        $cache_dir            = $this->container->getParameter('kernel.cache_dir');

        // Get the API payload to test.
        //$params['limit'] = 1000; // just in case we want to set this, or use a config parameter
        $em      = $this->dispatcher->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo    = $em->getRepository(\MauticPlugin\MauticContactLedgerBundle\Entity\CampaignSourceStats::class);

        $data       = $repo->getCampaignSourceTabData($params, $cache_dir);

        $headers    = [
            'mautic.contactledger.dashboard.source-revenue.header.sourceid',
            'mautic.contactledger.dashboard.source-revenue.header.sourcename',
            'mautic.contactledger.dashboard.source-revenue.header.utmsource',
            'mautic.contactledger.dashboard.source-revenue.header.received',
            'mautic.contactledger.dashboard.source-revenue.header.scrubbed',
            'mautic.contactledger.dashboard.source-revenue.header.declined',
            'mautic.contactledger.dashboard.source-revenue.header.converted',
            'mautic.contactledger.dashboard.source-revenue.header.revenue',
            'mautic.contactledger.dashboard.source-revenue.header.cost',
            'mautic.contactledger.dashboard.source-revenue.header.gm',
            'mautic.contactledger.dashboard.source-revenue.header.ecpm',
            'mautic.contactledger.dashboard.source-revenue.header.margin',
        ];
        foreach ($headers as $header) {
            $data['columns'][] = [
                'title' => $this->translator->trans($header),
            ];
        }
        $data = UTF8Helper::fixUTF8($data);

        return $this->sendJsonResponse($data);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function formatCurrency($value)
    {
        return sprintf('$%0.2f', floatval($value));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    protected function datatablesAction(Request $request)
    {
        $response         = [];
        $response['data'] = [];

        switch ($request->query->get('which', 'default')) {
            case 'campaign-ledger':

                /** @var \Mautic\CampaignBundle\Model\CampaignModel $campaignModel */
                $campaignModel = $this->get('mautic.campaign.model.campaign');
                $campaign      = $campaignModel->getEntity($request->query->get('campaignId'));

                $dateFrom = new \DateTime($request->query->get('date_from'));
                $dateTo   = new \DateTime($request->query->get('date_to'));

                /** @var \MauticPlugin\MauticContactLedgerBundle\Model\LedgerEntryModel $ledgerEntry */
                $ledgerEntry      = $this->get('mautic.contactledger.model.ledgerentry');
                $response['data'] = $ledgerEntry->getCampaignRevenueDatatableData($campaign, $dateFrom, $dateTo);

                break;
            default:
        }

        return $this->sendJsonResponse($response);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function getDateParams()
    {
        $session     = $this->get('session');
        $dateRange   = [];
        $fromSession = $session->get('mautic.daterange.form.from');
        $toSession   = $session->get('mautic.daterange.form.to');
        if (!empty($fromSession) && !empty($toSession)) {
            $dateRange = [
                'dateFrom' => new \DateTime($fromSession),
                'dateTo'   => new \DateTime($toSession),
            ];
        }

        if (empty($dateRange) || empty($dateRange['dateFrom'])) {
            // get System Default Date Ranges
            $dashboardModel = $this->get('mautic.dashboard.model.dashboard');
            $dateRange      = $dashboardModel->getDefaultFilter();
        }

        // clone so the session var doesnt get modified, just the values passed into tables
        $from      = clone $dateRange['dateFrom'];
        $to        = clone $dateRange['dateTo'];
        $params    =[];

        $from->setTimezone(new \DateTimeZone('UTC'));

        $to
            ->add(new \DateInterval('P1D'))
            ->sub(new \DateInterval('PT1S'))
            ->setTimezone(new \DateTimeZone('UTC'));

        $params['dateFrom'] = $from->format('Y-m-d H:i:s');

        $params['dateTo'] = $to->format('Y-m-d H:i:s');

        return $params;
    }
}
