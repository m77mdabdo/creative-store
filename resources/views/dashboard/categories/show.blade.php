@extends('dashboard.app.layout')

@section('title', 'Category Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1>Category Details</h1>

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
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>

                @if($canEdit)
                    <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit
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
                            <h3 class="card-title">Category Info</h3>
                        </div>

                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $category->id }}</p>
                            <p><strong>Arabic Name:</strong> {{ optional($category->translations->firstWhere('locale', 'ar'))->name ?? '-' }}</p>
                            <p><strong>English Name:</strong> {{ optional($category->translations->firstWhere('locale', 'en'))->name ?? '-' }}</p>
                            <p><strong>Slug:</strong> {{ $category->slug }}</p>
                            <p><strong>Parent:</strong> {{ $category->parent?->name ?? 'Root' }}</p>
                            <p><strong>Sort Order:</strong> {{ $category->sort_order }}</p>
                            <p>
                                <strong>Status:</strong>
                                @if($category->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Meta Title:</strong> {{ $category->meta_title ?: '-' }}</p>
                            <p><strong>Meta Description:</strong> {{ $category->meta_description ?: '-' }}</p>
                            <p><strong>Created At:</strong> {{ $category->created_at?->format('Y-m-d h:i A') }}</p>
                            <p><strong>Updated At:</strong> {{ $category->updated_at?->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Descriptions</h3>
                        </div>

                        <div class="card-body">
                            <h5>Arabic Description</h5>
                            <p>{{ optional($category->translations->firstWhere('locale', 'ar'))->description ?? '-' }}</p>

                            <hr>

                            <h5>English Description</h5>
                            <p>{{ optional($category->translations->firstWhere('locale', 'en'))->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Children Categories</h3>
                        </div>

                        <div class="card-body">
                            @forelse($category->children as $child)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $child->name ?? 'No Name' }}</strong><br>
                                    <small>{{ $child->slug }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No child categories.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Products Count</h3>
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
