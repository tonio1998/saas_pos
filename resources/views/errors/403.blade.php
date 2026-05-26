@extends('errors.layout')

@section('title', '403 | Access Denied')

@section('icon', '🔒')

@section('code', '403')

@section('heading', 'Access Denied')

@section(
    'message',
    $exception->getMessage()
        ?: 'You do not have permission to access this page.'
)
