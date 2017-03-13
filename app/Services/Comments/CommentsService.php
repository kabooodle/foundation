<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Comments;

use Kabooodle\Services\User\UserService;

/**
 * Class CommentsService
 */
class CommentsService
{
    /**
     * @var array
     */
    protected $mentions;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function getMentionsFromComment(string $string)
    {
        preg_match_all('/@([\w_\-\.]+)/', $string, $mentions);
        // [0] holds the matches, [1] holds the matches without the ampersand.
        return $mentions;
    }

    /**
     * @param array $mentions
     *
     * @return array
     */
    public function normalizeMentions(array $mentions)
    {
        return array_map(function(&$mention){
            return strtolower(trim($mention));
        }, array_unique($mentions));
    }

    /**
     * @param array $mentions
     *
     * @return mixed
     */
    public function getUsernamesFromMentions(array $mentions)
    {
        return $this->userService->repository->getByUsernames($this->normalizeMentions($mentions));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function highlightUsernames(string $string)
    {
        $mentions = $this->getMentionsFromComment($string);
        if ($mentions && count($mentions) > 0) {
            $usersMentioned = $this->getUsernamesFromMentions($mentions[1]);
            if ($usersMentioned) {
                foreach ($usersMentioned as $userMentioned) {
                    $mention = "@{$userMentioned->username}";
                    $string = str_ireplace($mention, '<span class="inline-user-mention inline-mention _600">'.$mention.'</span>', $string);
                }
            }
        }

        return $string;
    }
}
