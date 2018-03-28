<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic Community
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactLedgerBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticContactLedgerBundle\Model\LedgerEntryModel;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class LeadSubscriber.
 */
class LeadSubscriber extends CommonSubscriber
{
    /** @var LedgerEntryModel */
    protected $model;

    /** @var ContactLedgerContextSubscriber */
    protected $context;

    /** @var Logger */
    protected $logger;

    /**
     * LeadSubscriber constructor.
     *
     * @param LedgerEntryModel                    $model
     * @param ContactLedgerContextSubscriber|null $context
     * @param Logger|null                         $logger
     */
    public function __construct(
        LedgerEntryModel $model,
        ContactLedgerContextSubscriber $context = null,
        Logger $logger = null
    ) {
        $this->model   = $model;
        $this->context = $context;
        $this->logger  = $logger;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_POST_SAVE => ['postSaveAttributionCheck', -1],
        ];
    }

    /**
     * @param \Mautic\LeadBundle\Event\LeadEvent $event
     */
    public function postSaveAttributionCheck(LeadEvent $event)
    {
        $lead    = $event->getLead();
        $changes = $lead->getChanges(true);

        if (isset($changes['fields']) && isset($changes['fields']['attribution'])) {
            $oldValue = $changes['fields']['attribution'][0];
            $newValue = $changes['fields']['attribution'][1];
            // Ensure this is the latest change, even if it came from the PastChanges array on the contact.
            if ($oldValue !== $newValue && $newValue === $lead->getAttribution()) {
                $difference = $newValue - $oldValue;

                if ($this->logger) {
                    $this->logger->debug('Found an attribution change of: '.$difference);
                }

                $campaign = $this->context ? $this->context->getCampaign() : null;
                $actor    = $this->context ? $this->context->getActor() : null;
                $activity = $this->context ? $this->context->getActivity() : null;

                if ($difference > 0) {
                    $this->model->addEntry($lead, $campaign, $actor, $activity, null, $difference);
                } else {
                    if ($difference < 0) {
                        $this->model->addEntry($lead, $campaign, $actor, $activity, abs($difference));
                    }
                }

                unset($changes['fields']['attribution']);
                $lead->setChanges($changes);
            }
        }
    }
}
