<?php

namespace System\View\Traits;

trait HasExtendsContent
{

    private string $extendsContent;

    private function checkExtendsContent(): void
    {
        $layoutsFilePath = $this->findExtends();
        if ($layoutsFilePath) {
            $this->extendsContent = $this->viewLoader($layoutsFilePath);
            $yieldNamesArray = $this->findyieldNames();
            if ($yieldNamesArray) {
                foreach ($yieldNamesArray as $yieldName) {
                    $this->initialYields($yieldName);
                }
            }
            $this->content = $this->extendsContent;
        }
    }

    private function findExtends()
    {
        $filePathArray = [];
        preg_match("/s*@extends+\('([^)]+)'\)/", $this->content, $filePathArray);
        return $filePathArray[1] ?? false;
    }

    private function findYieldNames()
    {
        $yieldNamesArray = [];
        preg_match_all("/@yield+\('([^)]+)'\)/", $this->extendsContent, $yieldNamesArray, PREG_UNMATCHED_AS_NULL);
        return $yieldNamesArray[1] ?? false;
    }

    private function initialYields($yieldName): array|string
    {
        $string = $this->content;
        $startWord = "@section('$yieldName')";
        $endWord = "@endsection";
        $startPosition = strpos($string, $startWord);
        if ($startPosition === false) {
            return $this->extendsContent = str_replace("@section('$yieldName')", '', $this->extendsContent);
        }
        $startPosition += strlen($startWord);
        $endPosition = strpos($string, $endWord, $startPosition);
        if ($endPosition === false) {
            return $this->extendsContent = str_replace("@section('$yieldName')", '', $this->extendsContent);
        }

        $length = $endPosition - $startPosition;
        $sectionContent = substr($string, $startPosition, $length);
        return $this->extendsContent = str_replace("@section('$yieldName')", $sectionContent, $this->extendsContent);
    }

}