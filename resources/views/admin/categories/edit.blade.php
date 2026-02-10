@extends('admin.layout.app')

@section('title', 'Edit Category')
@section('header', 'Edit Category')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg max-w-lg">
        <div class="p-6">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                    <input type="text" name="title" id="title" value="{{ $category->title }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        required>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="status" class="form-checkbox h-5 w-5 text-indigo-600" {{ $category->status ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection