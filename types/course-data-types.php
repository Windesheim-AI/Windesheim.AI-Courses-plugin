<?php
class Block
{
    public $blockType;
    public $id;
    public $content;
}

class BlockOptions
{
    public $courseId;
    public $stageId;
}

class AIOptions
{
    public $prompt;
    public $provider;
    public $courseId;
    public $stageId;
}

class TextOptions
{
    public $text;
    public $courseId;
    public $stageId;
}

class ImageOptions
{
    public $url;
    public $courseId;
    public $stageId;
}

class VideoOptions
{
    public $url;
    public $courseId;
    public $stageId;
}

class ButtonOptions
{
    public $text;
    public $colorOptions;
    public $navigateToStageId;
    public $url;
    public $courseId;
    public $stageId;
}

class BlockType
{
    const Text = 'text';
    const AIGenerated = 'ai';
    const Image = 'image';
    const Video = 'video';
    const Button = 'button';
}

class Course
{
    public $title;
    public $id;
    public $description;
    public $stages;
}

class Stage
{
    public $id;
    public $title;
    public $description;
}
?>