@extends('dashboard.app.layout')

@section('title', 'Category Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1>{{ __('Category Details') }}</h1>

                @if(!empty($breadcrumb))
                    <small class="text-muted">
                        @foreach($breadcrumb as $item)
                            {{ $item['name'] }}@if(!$loop->last) &gt; @endif
                        @endforeach
                    </small>
                @endif
            </div>

            
            <div class="mt-2">
                <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>

                @if($canEdit)
                    <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Category Info') }}</h3>
                        </div>

                        <div class="card-body">
                            <p><strong>{{ __('ID:') }}</strong> {{ $category->id }}</p>
                            <p><strong>{{ __('Arabic Name:') }}</strong> {{ $category->translate('ar')?->name ?? '-' }}</p>
                            <p><strong>{{ __('English Name:') }}</strong> {{ $category->translate('en')?->name ?? '-' }}</p>
                            <p><strong>{{ __('Slug:') }}</strong> {{ $category->slug }}</p>
                            <p><strong>{{ __('Parent:') }}</strong> {{ $category->parent?->name ?? __('Root') }}</p>
                            <p><strong>{{ __('Sort Order:') }}</strong> {{ $category->sort_order }}</p>
                            <p>
                                <strong>{{ __('Status:') }}</strong>
                                @if($category->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </p>
                            <p><strong>{{ __('Meta Title:') }}</strong> {{ $category->meta_title ?: '-' }}</p>
                            <p><strong>{{ __('Meta Description:') }}</strong> {{ $category->meta_description ?: '-' }}</p>
                            <p><strong>{{ __('Created At:') }}</strong> {{ $category->created_at?->format('Y-m-d h:i A') }}</p>
                            <p><strong>{{ __('Updated At:') }}</strong> {{ $category->updated_at?->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Descriptions') }}</h3>
                        </div>

                        <div class="card-body">
                            <h5>{{ __('Arabic Description') }}</h5>
                            <p>{{ $category->translate('ar')?->description ?? '-' }}</p>

                            <hr>

                            <h5>{{ __('English Description') }}</h5>
                            <p>{{ $category->translate('en')?->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Children Categories') }}</h3>
                        </div>

                        <div class="card-body">
                            @forelse($category->children as $child)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $child->name ?? __('No Name') }}</strong><br>
                                    <small>{{ $child->slug }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No child categories.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Products Count') }}</h3>
                        </div>

                        <div class="card-body">
                            <h3>{{ $category->products_count }}</h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
