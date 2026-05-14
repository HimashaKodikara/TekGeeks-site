<?php

namespace App\Helpers;

class LangHelper
{
    public static function setLangName($modelOrCollection, $baseField, $lang)
    {
        if (!$modelOrCollection) return;

        // Check if it is a collection
        if ($modelOrCollection instanceof \Illuminate\Support\Collection) {
            return $modelOrCollection->map(function ($model) use ($baseField, $lang) {
                $field = $baseField . '_' . $lang;
                $model->name = $model->{$field} ?? $model->{$baseField . '_en'};

                // Optional: remove other language fields
                unset($model->{$baseField . '_en'}, $model->{$baseField . '_si'}, $model->{$baseField . '_ta'});

                return $model;
            });
        }

        // Single model
        $field = $baseField . '_' . $lang;
        $modelOrCollection->name = $modelOrCollection->{$field} ?? $modelOrCollection->{$baseField . '_en'};

        unset($modelOrCollection->{$baseField . '_en'}, $modelOrCollection->{$baseField . '_si'}, $modelOrCollection->{$baseField . '_ta'});

        return $modelOrCollection;
    }
}
