<?php

namespace App\lib\Http;

interface IRequest
{
    public function get(string $var): ?string;

    public function getAll(): array;

    public function getMethod(): string;

    public function getUri(): string;

    public function getQuery(): string;

    public function getUrl(): string;
}