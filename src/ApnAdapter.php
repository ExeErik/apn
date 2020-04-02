<?php

namespace NotificationChannels\Apn;

use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Pushok\Payload\Sound;

class ApnAdapter
{
    /**
     * Convert an ApnMessage instance into a Zend Apns Message.
     *
     * @param  \NotificationChannels\Apn\ApnMessage  $message
     * @param  string  $token
     * @return \Pushok\Notification
     */
    public function adapt(ApnMessage $message, string $token)
    {
        $alert = Alert::create();
        $soundcritcal = Sound::create();

        if ($title = $message->title) {
            $alert->setTitle($title);
        }

        if ($body = $message->body) {
            $alert->setBody($body);
        }

        $payload = Payload::create()
            ->setAlert($alert)
            ->setContentAvailability((bool) $message->contentAvailable)
            ->setMutableContent((bool) $message->mutableContent);

        if ($badge = $message->badge) {
            $payload->setBadge($badge);
        }
        if ($message->criticalEnabled == 1){
            if ($criticalEnabled = $message->criticalEnabled){
                $soundcritcal->setCriticalSoundEnabled($criticalEnabled);
            }
            if ($criticalVolume = $message->criticalVolume){
                $soundcritcal->setCriticalSoundVolume($criticalVolume);
            }
            if ($criticalName = $message->criticalName){
                $soundcritcal->setCriticalSoundName($criticalName);
            }
            $payload->setSound($soundcritcal);
        }else {
            if ($sound = $message->sound) {
                $payload->setSound($sound);
            }
        }


        if ($category = $message->category) {
            $payload->setCategory($category);
        }

        foreach ($message->custom as $key => $value) {
            $payload->setCustomValue($key, $value);
        }

        if ($pushType = $message->pushType) {
            $payload->setPushType($pushType);
        }

        $notification = new Notification($payload, $token);

        if ($expiresAt = $message->expiresAt) {
            $notification->setExpirationAt($expiresAt);
        }

        return $notification;
    }
}
