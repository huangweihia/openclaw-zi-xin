<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comment;
use App\Models\EmailSubscription;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Support\Str;

class SystemNotificationService
{
    /**
     * 仅当用户在邮件订阅偏好中开启「系统通知」且未退订时创建站内通知；否则不生成记录。
     */
    public function notify(
        User $recipient,
        string $type,
        string $title,
        ?string $body = null,
        array $meta = [],
        bool $isFromAdmin = false
    ): ?SystemNotification {
        if (! EmailSubscription::wantsSystemNotifications($recipient)) {
            return null;
        }

        return SystemNotification::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'meta' => $meta ?: null,
            'is_from_admin' => $isFromAdmin,
        ]);
    }

    public function notifyArticleLiked(Article $article, User $actor): void
    {
        if (!$article->author_id || (int) $article->author_id === (int) $actor->id) {
            return;
        }

        $author = User::find($article->author_id);
        if (!$author) {
            return;
        }

        $this->notify(
            $author,
            'article_liked',
            $actor->name . ' 点赞了你的文章',
            '《' . $article->title . '》',
            [
                'article_id' => $article->id,
                'actor_id' => $actor->id,
            ],
            false
        );
    }

    public function notifyArticleFavorited(Article $article, User $actor): void
    {
        if (!$article->author_id || (int) $article->author_id === (int) $actor->id) {
            return;
        }

        $author = User::find($article->author_id);
        if (!$author) {
            return;
        }

        $this->notify(
            $author,
            'article_favorited',
            $actor->name . ' 收藏了你的文章',
            '《' . $article->title . '》',
            [
                'article_id' => $article->id,
                'actor_id' => $actor->id,
            ],
            false
        );
    }

    /**
     * 文章下有新评论或回复时通知文章作者（本人评论不发）
     */
    public function notifyArticleCommented(Article $article, User $actor, Comment $comment): void
    {
        if (! $article->author_id || (int) $article->author_id === (int) $actor->id) {
            return;
        }

        $author = User::find($article->author_id);
        if (! $author) {
            return;
        }

        $isReply = $comment->parent_id !== null;
        $title = $isReply
            ? $actor->name . ' 在你的文章下发表了回复'
            : $actor->name . ' 评论了你的文章';

        $snippet = Str::limit(strip_tags((string) $comment->content), 120);
        $body = '《' . $article->title . '》' . ($snippet !== '' ? "\n" . $snippet : '');

        $this->notify(
            $author,
            'article_commented',
            $title,
            $body,
            [
                'article_id' => $article->id,
                'actor_id' => $actor->id,
                'comment_id' => $comment->id,
                'parent_id' => $comment->parent_id,
            ],
            false
        );
    }

    /**
     * 有人回复了某条评论时通知被回复者（本人回复不发；若被回复者即文章作者则已由 notifyArticleCommented 通知，避免重复）
     */
    public function notifyCommentReplied(Article $article, User $actor, Comment $comment, Comment $parentComment): void
    {
        if ((int) $parentComment->user_id === (int) $actor->id) {
            return;
        }

        if ($article->author_id && (int) $parentComment->user_id === (int) $article->author_id) {
            return;
        }

        $recipient = User::find($parentComment->user_id);
        if (! $recipient) {
            return;
        }

        $snippet = Str::limit(strip_tags((string) $comment->content), 120);
        $body = '《' . $article->title . '》' . ($snippet !== '' ? "\n" . $snippet : '');

        $this->notify(
            $recipient,
            'comment_replied',
            $actor->name . ' 回复了你的评论',
            $body,
            [
                'article_id' => $article->id,
                'actor_id' => $actor->id,
                'comment_id' => $comment->id,
                'parent_id' => $parentComment->id,
            ],
            false
        );
    }
}
