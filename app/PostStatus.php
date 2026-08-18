<?php

namespace App;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
