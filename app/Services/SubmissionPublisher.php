<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ContentSubmission;
use App\Models\Job;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeDocument;
use App\Models\Project;

class SubmissionPublisher
{
    public static function publish(ContentSubmission $submission): void
    {
        if ($submission->status !== 'approved') {
            return;
        }

        if ($submission->published_model_type && $submission->published_model_id) {
            return;
        }

        $target = match ($submission->type) {
            'document' => self::publishAsArticle($submission),
            'project' => self::publishAsProject($submission),
            'job' => self::publishAsJob($submission),
            'knowledge' => self::publishAsKnowledge($submission),
            default => null,
        };

        if ($target) {
            $submission->published_model_type = get_class($target);
            $submission->published_model_id = $target->id;
            $submission->published_at = $submission->published_at ?: now();
            $submission->save();
        }
    }

    private static function publishAsArticle(ContentSubmission $submission): Article
    {
        $vip = (bool) $submission->is_paid;

        return Article::create([
            'title' => $submission->title,
            'summary' => $submission->summary,
            'content' => $submission->content,
            'author_id' => $submission->user_id,
            'is_premium' => $vip,
            'is_vip' => $vip,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    private static function publishAsProject(ContentSubmission $submission): Project
    {
        $vip = (bool) $submission->is_paid;
        $body = $submission->content ?: '';

        return Project::create([
            'name' => $submission->title,
            'full_name' => $submission->title,
            'description' => $body ?: ($submission->summary ?? ''),
            'url' => 'https://community.local/submission-project/' . $submission->id . '-' . uniqid('', true),
            'monetization' => $vip ? ('付费内容：' . $submission->price . ' ' . $submission->currency) : '社区投稿',
            'difficulty' => 3,
            'stars' => 0,
            'forks' => 0,
            'score' => 0,
            'is_vip' => $vip,
            'collected_at' => now(),
        ]);
    }

    /**
     * 同步到前台「职位」表 positions（App\Models\Job）
     */
    private static function publishAsJob(ContentSubmission $submission): Job
    {
        $p = $submission->payload ?? [];

        return Job::create([
            'user_id' => $submission->user_id,
            'title' => $p['job_title'] ?? $submission->title,
            'company_name' => $p['company_name'] ?? '社区投稿',
            'location' => $p['location'] ?? null,
            'salary_range' => $p['salary_range'] ?? null,
            'requirements' => $p['job_requirements'] ?? null,
            'description' => $submission->content,
            'source_url' => $p['source_url'] ?? $p['url'] ?? null,
            'is_contact_vip' => (bool) $submission->is_paid,
            'is_vip_only' => (bool) $submission->is_paid,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    private static function publishAsKnowledge(ContentSubmission $submission): KnowledgeBase
    {
        $p = $submission->payload ?? [];
        $category = is_string($p['knowledge_category'] ?? null)
            ? $p['knowledge_category']
            : 'general';

        $base = KnowledgeBase::create([
            'user_id' => $submission->user_id,
            'title' => $submission->title,
            'description' => $submission->summary,
            'category' => $category,
            'is_public' => true,
            'is_vip_only' => (bool) $submission->is_paid,
        ]);

        $html = $submission->content ?: '';
        KnowledgeDocument::create([
            'knowledge_base_id' => $base->id,
            'title' => $submission->title,
            'content' => $html,
            'file_type' => 'html',
            'file_size' => strlen($html),
        ]);

        return $base;
    }
}
