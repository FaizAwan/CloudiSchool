<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    /**
     * Analyze exam marks and provide insights.
     *
     * @param array $data
     * @return string
     */
    public function analyzeMarks(array $data)
    {
        if (!$this->apiKey) {
            return "Error: OpenAI API key is missing. Please configure it in .env.";
        }

        $prompt = $this->buildPrompt($data);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo', // Cost-effective model
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an educational assistant analyzing student exam performance. Provide concise, encouraging, and actionable insights based on the provided marks. Highlight top performers and students who need attention. Format your response in HTML commands (e.g. <b>, <ul>, <li>) so it can be directly verified.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return $content ?? 'No analysis generated.';
            }
            else {
                Log::error('OpenAI API Error: ' . $response->body());
                return "Error analyzing data: " . $response->json('error.message', 'Unknown error');
            }
        }
        catch (\Exception $e) {
            Log::error('OpenAI Exception: ' . $e->getMessage());
            return "Server Error: " . $e->getMessage();
        }
    }

    /**
     * Extract marks from an image of a filled sheet.
     *
     * @param string $imagePath
     * @param array $context
     * @return array|string
     */
    public function extractDataFromImage($imagePath, $context = [])
    {
        if (!$this->apiKey) {
            return "Error: OpenAI API key is missing.";
        }

        try {
            // Read image and convert to base64
            $imageData = file_get_contents($imagePath);
            $base64Image = base64_encode($imageData);
            $mimeType = mime_content_type($imagePath);

            $subjectsList = !empty($context['subjects']) ? implode(', ', $context['subjects']) : 'Unknown';
            $className = $context['class'] ?? 'Unknown';
            $section = $context['section'] ?? 'Unknown';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o', // Required for Vision/Image processing
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a data extraction assistant for a school management system.
                        Extract student marks from an exam sheet image for Class: $className, Section: $section.
                        Expected Subjects: $subjectsList.
                        
                        Return ONLY a valid JSON array of objects. Each object should have:
                        - \"grno\": The student's GR Number or Roll Number.
                        - \"marks\": An object where keys are EXACTLY one of the expected subjects ($subjectsList) and values are the marks obtained.
                        
                        Rules:
                        1. If a value is 'A' or 'Abs', return 'A'.
                        2. If a value is numeric, return as numeric.
                        3. Match the GR Numbers carefully.
                        4. Do not wrap the JSON in markdown code blocks. Just valid JSON."
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => "Extract the student marks from this image for Class: $className, Section: $section. The subjects in this form are: $subjectsList."
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:$mimeType;base64,$base64Image"
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 2000,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $decoded = $this->parseJsonResponse($content);
                return $decoded ?? "Error: Could not parse JSON response.";
            }
            else {
                Log::error('OpenAI Vision Error: ' . $response->body());
                return "Error processing image: " . $response->json('error.message', 'Unknown error');
            }
        }
        catch (\Exception $e) {
            Log::error('OpenAI Exception: ' . $e->getMessage());
            return "Server Error: " . $e->getMessage();
        }
    }

    /**
     * Generate blog post content and SEO metadata based on a prompt/topic.
     *
     * @param string $topic
     * @param string $niche
     * @return array|string
     */
    public function generateBlogPost($topic, $niche = 'Education')
    {
        if (!$this->apiKey) {
            return "Error: OpenAI API key is missing.";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o', // Using GPT-4o for high quality content
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert SEO content creator and blogger. 
                        Your task is to generate a high-quality, professional, and eye-catchy blog post based on the user's input.
                        The content must be 100% SEO optimized and SEO friendly.
                        
                        Return ONLY a valid JSON object with the following keys:
                        - \"title\": A catchy, SEO-friendly title.
                        - \"content\": The full blog post content in professional HTML format (use <h2>, <p>, <ul>, <strong>, etc.). Include a proper introduction and conclusion.
                        - \"meta_title\": A perfectly optimized SEO meta title (max 60 chars).
                        - \"meta_description\": A compelling SEO meta description (max 160 chars).
                        - \"meta_keywords\": A comma-separated string of relevant SEO keywords.
                        
                        Niche: $niche.
                        The tone should be professional yet engaging."
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a blog post about: $topic"
                    ]
                ],
                'temperature' => 0.8,
                'max_tokens' => 3000,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $decoded = $this->parseJsonResponse($content);
                return $decoded ?? "Error: Could not parse JSON response.";
            }
            else {
                Log::error('OpenAI Blog Generation Error: ' . $response->body());
                return "Error generating blog: " . $response->json('error.message', 'Unknown error');
            }
        }
        catch (\Exception $e) {
            Log::error('OpenAI Exception: ' . $e->getMessage());
            return "Server Error: " . $e->getMessage();
        }
    }

    /**
     * Generate 5 title suggestions based on a topic.
     *
     * @param string $topic
     * @param string $niche
     * @return array|string
     */
    public function generateTitleSuggestions($topic, $niche = 'Education')
    {
        if (!$this->apiKey) {
            return "Error: OpenAI API key is missing.";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert SEO content strategist specializing in $niche.
                        Your task is to generate 5 catchy, SEO-optimized blog post titles based on the user's topic.
                        
                        Return ONLY a valid JSON object with this structure:
                        {
                            \"titles\": [
                                \"Title 1 - Catchy and SEO optimized\",
                                \"Title 2 - Catchy and SEO optimized\",
                                \"Title 3 - Catchy and SEO optimized\",
                                \"Title 4 - Catchy and SEO optimized\",
                                \"Title 5 - Catchy and SEO optimized\"
                            ]
                        }
                        
                        Each title should be:
                        - Engaging and click-worthy
                        - SEO-friendly with relevant keywords
                        - Professional yet appealing
                        - Between 40-60 characters
                        - Unique and creative"
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate 5 blog title suggestions about: $topic"
                    ]
                ],
                'temperature' => 0.9,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $decoded = $this->parseJsonResponse($content);
                return $decoded ?? "Error: Could not parse JSON response.";
            }
            else {
                Log::error('OpenAI Title Generation Error: ' . $response->body());
                return "Error generating titles: " . $response->json('error.message', 'Unknown error');
            }
        }
        catch (\Exception $e) {
            Log::error('OpenAI Exception: ' . $e->getMessage());
            return "Server Error: " . $e->getMessage();
        }
    }

    private function buildPrompt($data)
    {
        $subjectNames = $data['subjects'] ?? [];
        $students = $data['students'] ?? [];
        $className = $data['class'] ?? 'Unknown Class';
        $section = $data['section'] ?? 'Unknown Section';

        $text = "Analyze the exam results for Class: $className, Section: $section.\n\n";
        $text .= "Subjects: " . implode(', ', $subjectNames) . "\n\n";
        $text .= "Student Data:\n";

        $count = 0;
        foreach ($students as $student) {
            // Limit to 20 students to avoid token limits if list is huge
            if ($count++ > 20)
                break;

            $name = $student['name'] ?? 'Student';
            $marks = $student['marks'] ?? [];
            $marksStr = [];
            foreach ($marks as $subject => $score) {
                $marksStr[] = "$subject: $score";
            }
            $text .= "- $name: " . implode(', ', $marksStr) . "\n";
        }

        $text .= "\nProvide a summary of performance, highlighting any general trends, subjects where the class is struggling, and specific students who excelled or need help.";

        return $text;
    }

    /**
     * More robust JSON parsing from AI responses.
     */
    private function parseJsonResponse($content)
    {
        // Clean markdown if present
        $content = str_replace(['```json', '```'], '', $content);
        $content = trim($content);

        // Attempt to find JSON if there's surrounding text
        $firstBrace = strpos($content, '{');
        $firstBracket = strpos($content, '[');
        
        $start = false;
        if ($firstBrace !== false && $firstBracket !== false) {
            $start = min($firstBrace, $firstBracket);
        } else {
            $start = ($firstBrace !== false) ? $firstBrace : $firstBracket;
        }

        $lastBrace = strrpos($content, '}');
        $lastBracket = strrpos($content, ']');
        
        $end = false;
        if ($lastBrace !== false && $lastBracket !== false) {
            $end = max($lastBrace, $lastBracket);
        } else {
            $end = ($lastBrace !== false) ? $lastBrace : $lastBracket;
        }

        if ($start !== false && $end !== false && $end > $start) {
            $content = substr($content, $start, $end - $start + 1);
        }

        return json_decode($content, true);
    }
}
