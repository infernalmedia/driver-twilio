<?php

namespace BotMan\Drivers\Twilio;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\Twilio\TwilioMessageDriver;

class TwilioImageDriver extends TwilioMessageDriver
{
    const DRIVER_NAME = 'TwilioImage';

    public function matchesRequest()
    {
        $hasAttachments = $this->event->has('NumMedia') && intval($this->event->get('NumMedia')) > 0;
        return $hasAttachments && $this->event->has('MessageSid') && $this->isSignatureValid();
    }

    public function getMessages()
    {
        if (empty($this->messages)) {
            $this->loadMessages();
        }

        return $this->messages;
    }

    public function loadMessages()
    {
        $message = new IncomingMessage(
            Image::PATTERN,
            $this->event->get('From'),
            $this->event->get('To')
        );
        $message->setImages($this->getImages());

        $this->messages = [$message];
    }

    private function getImages()
    {
        $images = [];
        $nbImages = intval($this->event->get('NumMedia'));

        for ($i = 0; $i < $nbImages; $i++) {
            $images[] = new Image($this->event->get('MediaUrl' . $i));
        }

        return $images;
    }
}
