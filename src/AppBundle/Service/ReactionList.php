<?php
namespace AppBundle\Service;

use AppBundle\Entity\Reaction;
use AppBundle\Entity\UserSlack;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\MessageAnswerSlack;
use AppBundle\Entity\FileSlack;

class ReactionList
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function addReactions($target, $reactions, $fullUpdate = false)
    {
        if($fullUpdate)
        {
            $target->getReactions()->clear();
        }
        foreach($reactions as $reaction)
        {
            $newReaction = new Reaction();
            $newReaction->setName($reaction->name);
            foreach($reaction->users as $userReaction)
            {
                $userSlack = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($userReaction);
                $newReaction->addUser($userSlack);

            }
            $target->addReaction($newReaction);
        }
        return $target;
    }
    private function addReaction($reaction)
    {
        $newReaction = new Reaction();
        $newReaction->setName($reaction->reaction);
        return $newReaction;

    }
    private function addUser($user, Reaction $reaction)
    {
       $userSlack = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($user);
       if($userSlack)
       {
           $reaction->addUser($userSlack);
       }
       return $reaction;

    }
    private function removeUser($user, Reaction $reaction)
    {
        $userSlack = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($user);
        if($userSlack)
        {
            $reaction->removeUser($userSlack);
        }
        return $reaction;

    }
    public function addReactionToTarget($content, $target)
    {
        $reactions = $target->getReactions();
        foreach($reactions as $key => $reaction)
        {
            if($reaction->getName() === $content->event->reaction)
            {
                $reactions[$key] = $this->addUser($content->event->user, $reaction);
                $target->setReactions($reactions);
                $this->em->flush();
                return true;
            }
        }
        $reaction = $this->addReaction($content->event);
        $reaction = $this->addUser($content->event->user, $reaction);
        $target->addReaction($reaction);
        $this->em->flush();

    }
    public function removeReactionToTarget($content, $target)
    {
        $reactions = $target->getReactions();
        foreach($reactions as $key => $reaction)
        {
            if($reaction->getName() === $content->event->reaction)
            {
                $reaction = $this->removeUser($content->event->user, $reaction);

                if(count($reaction->getUsers()) <= 0)
                {
                    $target->removeReaction($reaction);
                }
                else {
                    $reactions[$key] = $reaction;
                    $target->setReactions($reactions);
                }

                $this->em->flush();
                return true;
            }
        }
        return false;



    }
    public function getAcceptedType()
    {
        return  [
            "message" => MessageSlack::class,
            "answer" => MessageAnswerSlack::class,
            "file" => FileSlack::class
        ];
    }
}