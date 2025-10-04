@extends('layouts.app')

@section('title', 'Edit News - Office Admin')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="dashboard">
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()" style="display: none; position: fixed; top: 1rem; left: 1rem; z-index: 1001; background: var(--primary-color); color: white; border: none; padding: 0.75rem; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
        <i class="fas fa-bars"></i>
    </button>

   <!-- Replace the current sidebar section with this updated version -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>
            @php
                $office = Auth::guard('admin')->user()->office ?? 'OFFICE';
            @endphp
            @if($office === 'NSTP')
                <i class="fas fa-flag"></i>
            @elseif($office === 'SSC')
                <i class="fas fa-users"></i>
            @elseif($office === 'GUIDANCE')
                <i class="fas fa-heart"></i>
            @elseif($office === 'REGISTRAR')
                <i class="fas fa-file-alt"></i>
            @elseif($office === 'CLINIC')
                <i class="fas fa-stethoscope"></i>
            @else
                <i class="fas fa-briefcase"></i>
            @endif
            {{ $office }} Office
        </h3>
        <div class="office-info">{{ auth('admin')->user()->username }}</div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="{{ route('office-admin.dashboard') }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a></li>
        <li><a href="{{ route('office-admin.announcements.index') }}">
            <i class="fas fa-bullhorn"></i> Announcements
        </a></li>
        <li><a href="{{ route('office-admin.events.index') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a></li>
        <li><a href="{{ route('office-admin.news.index') }}" class="active">
            <i class="fas fa-newspaper"></i> News
        </a></li>
    </ul>
</div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1><i class="fas fa-edit"></i> Edit News Article</h1>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">Update news article from {{ Auth::guard('admin')->user()->office_display }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('office-admin.news.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to News
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="form-container">
            <form method="POST" action="{{ route('office-admin.news.update', $news->id) }}" enctype="multipart/form-data" class="news-form">
                @csrf
                @method('PUT')
                
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Article Information</h3>
                    
                    <div class="form-group">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading"></i> Article Title *
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-input @error('title') error @enderror" 
                               value="{{ old('title', $news->title) }}" 
                               placeholder="Enter news article title..."
                               required>
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content" class="form-label">
                            <i class="fas fa-align-left"></i> Article Content *
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  class="form-textarea @error('content') error @enderror" 
                                  rows="10" 
                                  placeholder="Write your news article content here..."
                                  required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Article Information -->
                <div class="form-section">
                    <h3><i class="fas fa-clock"></i> Article Information</h3>
                    <div class="creation-info">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar"></i> Created On
                            </div>
                            <div class="info-value">
                                {{ $news->created_at->format('F d, Y') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clock"></i> Created At
                            </div>
                            <div class="info-value">
                                {{ $news->created_at->format('g:i A') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i> Created By
                            </div>
                            <div class="info-value">
                                {{ $news->admin->username ?? 'Unknown' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-briefcase"></i> Office
                            </div>
                            <div class="info-value">
                                {{ Auth::guard('admin')->user()->office_display }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-edit"></i> Last Updated
                            </div>
                            <div class="info-value">
                                {{ $news->updated_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-eye"></i> Status
                            </div>
                            <div class="info-value">
                                @if($news->is_published)
                                    <span style="color: var(--success-color); font-weight: 600;">Published</span>
                                @else
                                    <span style="color: var(--warning-color); font-weight: 600;">Draft</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-image"></i> Media Files (Optional)</h3>
                    
                    <!-- Current Files Display -->
                    @php
                        $currentImages = [];
                        $currentVideos = [];
                        
                        // Handle legacy single image
                        if($news->image_path) {
                            $currentImages[] = $news->image_path;
                        }
                        
                        // Handle new multiple images
                        if($news->image_paths) {
                            $imagePaths = is_string($news->image_paths) ? json_decode($news->image_paths, true) : $news->image_paths;
                            if(is_array($imagePaths)) {
                                $currentImages = array_merge($currentImages, $imagePaths);
                            }
                        }
                        
                        // Handle legacy single video
                        if($news->video_path) {
                            $currentVideos[] = $news->video_path;
                        }
                        
                        // Handle new multiple videos
                        if($news->video_paths) {
                            $videoPaths = is_string($news->video_paths) ? json_decode($news->video_paths, true) : $news->video_paths;
                            if(is_array($videoPaths)) {
                                $currentVideos = array_merge($currentVideos, $videoPaths);
                            }
                        }
                    @endphp
                    
                    @if(count($currentImages) > 0)
                       <div class="current-media-section">
                           <h4><i class="fas fa-image"></i> Current Images ({{ count($currentImages) }})</h4>
                           <div class="current-media-grid">
                               @foreach($currentImages as $index => $imagePath)
                                   @if(Storage::disk('public')->exists($imagePath))
                                       <div class="current-media-item">
                                           <img src="{{ asset('storage/' . $imagePath) }}" alt="Current Image">
                                           <div class="remove-media-checkbox">
                                               <input type="checkbox" name="remove_images[]" value="{{ $index }}" id="remove_image_{{ $index }}">
                                               <label for="remove_image_{{ $index }}"><i class="fas fa-trash-alt"></i> Remove</label>
                                           </div>
                                       </div>
                                   @endif
                               @endforeach
                           </div>
                       </div>
                   @endif

                   @if(count($currentVideos) > 0)
                       <div class="current-media-section">
                           <h4><i class="fas fa-video"></i> Current Videos ({{ count($currentVideos) }})</h4>
                           <div class="current-media-grid">
                               @foreach($currentVideos as $index => $videoPath)
                                   @if(Storage::disk('public')->exists($videoPath))
                                       <div class="current-media-item video-media-item">
                                           <div class="video-container">
                                               <video controls preload="metadata" class="current-video">
                                                   <source src="{{ asset('storage/' . $videoPath) }}" type="video/mp4">
                                                   Your browser does not support the video tag.
                                               </video>
                                               <div class="video-overlay">
                                                   <i class="fas fa-play-circle"></i>
                                               </div>
                                           </div>
                                           <div class="video-info">
                                               <span class="video-filename">{{ basename($videoPath) }}</span>
                                           </div>
                                           <div class="remove-media-checkbox">
                                               <input type="checkbox" name="remove_videos[]" value="{{ $index }}" id="remove_video_{{ $index }}">
                                               <label for="remove_video_{{ $index }}"><i class="fas fa-trash-alt"></i> Remove</label>
                                           </div>
                                       </div>
                                   @endif
                               @endforeach
                           </div>
                       </div>
                   @endif
                    
                    <!-- Multiple Images Upload -->
                    <div class="form-group">
                        <label for="images" class="form-label">
                            <i class="fas fa-images"></i> Images (Up to 2)
                        </label>
                        <div class="file-upload-area" id="imagesUploadArea">
                            <input type="file"
                                   id="images"
                                   name="images[]"
                                   class="file-input @error('images') error @enderror @error('images.*') error @enderror"
                                   accept="image/jpeg,image/png,image/jpg"
                                   multiple>
                            <div class="file-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <small>JPG, PNG up to 5MB each (Max: 2 images)</small>
                            </div>
                        </div>
                        <div id="imagesPreview" class="file-preview-container"></div>
                        @error('images')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error('images.*')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Multiple Videos Upload -->
                    <div class="form-group">
                        <label for="videos" class="form-label">
                            <i class="fas fa-video"></i> Video (Up to 1)
                        </label>
                        <div class="file-upload-area" id="videosUploadArea">
                            <input type="file"
                                   id="videos"
                                   name="videos[]"
                                   class="file-input @error('videos') error @enderror @error('videos.*') error @enderror"
                                   accept="video/mp4"
                                   multiple>
                            <div class="file-upload-content">
                                <i class="fas fa-video"></i>
                                <p>Click to upload or drag and drop</p>
                                <small>MP4 up to 50MB, maximum 1 video</small>
                            </div>
                        </div>
                        <div id="videosPreview" class="file-preview-container"></div>
                        @error('videos')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error('videos.*')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-cog"></i> Publishing Settings</h3>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox"
                                       name="is_published"
                                       value="1"
                                       {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                                       class="checkbox-input">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">
                                    <i class="fas fa-eye"></i> Publish immediately
                                </span>
                            </label>
                            <small class="form-help">If checked, the news will appear in all departments. If unchecked, the news will be saved as a draft</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update News Article
                    </button>
                    <a href="{{ route('office-admin.news.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
   :root {
        /* Keep your existing variables and add these */
        --sidebar-width: 280px;
        --header-height: 80px;
        --transition-speed: 0.3s;
        --border-radius: 12px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        --card-padding: 1.5rem;

        /* Dynamic theming based on office */
        @php $office = auth('admin')->user()->office; @endphp
        @if($office === 'NSTP')
            --primary-color: #10b981;
            --primary-light: #d1fae5;
            --primary-dark: #059669;
            --primary-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        @elseif($office === 'SSC')
            --primary-color: #3b82f6;
            --primary-light: #dbeafe;
            --primary-dark: #2563eb;
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        @elseif($office === 'GUIDANCE')
            --primary-color: #8b5cf6;
            --primary-light: #ede9fe;
            --primary-dark: #7c3aed;
            --primary-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        @elseif($office === 'REGISTRAR')
            --primary-color: #f59e0b;
            --primary-light: #fef3c7;
            --primary-dark: #d97706;
            --primary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        @elseif($office === 'CLINIC')
            --primary-color: #ef4444;
            --primary-light: #fee2e2;
            --primary-dark: #dc2626;
            --primary-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        @else
            --primary-color: #667eea;
            --primary-light: #e0e7ff;
            --primary-dark: #5b67d7;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        @endif

        --bg-sidebar: #1f2937;
        --bg-sidebar-hover: rgba(255, 255, 255, 0.08);

        /* Additional required variables */
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;

        --text-primary: #111827;
        --text-secondary: #4b5563;
        --text-muted: #6b7280;
        --text-light: #9ca3af;

        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f1f5f9;

        --border-color: #e5e7eb;
        --border-light: #f3f4f6;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        line-height: 1.6;
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
    }

    /* Update the sidebar styles */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--bg-sidebar);
        color: white;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: var(--box-shadow);
        transition: transform var(--transition-speed) ease;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        margin-bottom: 0.5rem;
        background: var(--bg-sidebar);
    }

    .sidebar-header h3 {
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .sidebar-header h3 i {
        color: var(--primary-color);
        font-size: 1.3rem;
    }

    .office-info {
        color: var(--text-light);
        font-size: 0.8rem;
        margin-top: 0.5rem;
        font-weight: 400;
        opacity: 0.8;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0.5rem 0;
        flex-grow: 1;
    }

    .sidebar-menu li {
        margin: 0.25rem 0;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-weight: 500;
        transition: all var(--transition-speed) ease;
        gap: 0.75rem;
        font-size: 0.9rem;
        border-left: 3px solid transparent;
        margin: 0 0.5rem;
        border-radius: 6px;
    }

    .sidebar-menu a:hover {
        background: var(--bg-sidebar-hover);
        color: white;
        border-left-color: var(--primary-color);
    }

    .sidebar-menu a.active {
        background: var(--bg-sidebar-hover);
        color: white;
        border-left-color: var(--primary-color);
        font-weight: 600;
    }

    .sidebar-menu a i {
        width: 20px;
        text-align: center;
        font-size: 0.95rem;
    }

    .logout-btn {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        width: calc(100% - 1rem);
        text-align: left;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: all var(--transition-speed) ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        margin: 0.5rem;
        border-radius: 6px;
    }

    .logout-btn:hover {
        background: var(--bg-sidebar-hover);
        color: white;
    }

    .logout-btn i {
        width: 20px;
        text-align: center;
    }

    /* Mobile styles */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 2rem;
        background: var(--bg-secondary);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        background: var(--bg-primary);
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header h1 i {
        color: var(--primary-color);
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        line-height: 1;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--border-color);
        color: var(--text-primary);
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .alert {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-radius: var(--radius-lg);
        border: 1px solid;
    }

    .alert-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .alert-icon {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }

    .alert-content {
        flex: 1;
    }

    .alert-content strong {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-container {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .form-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h3 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    .form-section h3 i {
        color: var(--primary-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-label i {
        color: var(--primary-color);
        width: 16px;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .form-input.error,
    .form-textarea.error {
        border-color: var(--danger-color);
    }

    .form-textarea {
        resize: vertical;
        min-height: 150px;
    }

    .form-help {
        display: block;
        margin-top: 0.5rem;
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .error-message {
        display: block;
        margin-top: 0.5rem;
        color: var(--danger-color);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .creation-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        background: var(--bg-tertiary);
        padding: 1.5rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .current-file {
        background: var(--bg-tertiary);
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
    }

    .current-file h4 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .current-file h4 i {
        color: var(--primary-color);
    }



    /* Checkbox Group Styling */
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        padding: 1rem;
        border: 2px solid var(--border-color);
        border-radius: var(--radius-lg);
        transition: all 0.3s ease;
        background: var(--bg-secondary);
    }

    .checkbox-label:hover {
        border-color: var(--primary-color);
        background: #fef3c7;
    }

    .checkbox-input {
        display: none;
    }

    .checkbox-custom {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: var(--radius-sm);
        position: relative;
        transition: all 0.3s ease;
        background: white;
    }

    .checkbox-input:checked + .checkbox-custom {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    .checkbox-input:checked + .checkbox-custom::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .checkbox-text {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        color: var(--text-primary);
    }

    .checkbox-text i {
        color: var(--primary-color);
    }

    /* File Upload Styling */
    .file-upload-area {
        position: relative;
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: var(--bg-secondary);
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: var(--primary-color);
        background: #fef3c7;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-content {
        pointer-events: none;
    }

    .file-upload-content i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .file-upload-content p {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .file-upload-content small {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .form-actions {
        padding: 2rem;
        background: var(--bg-tertiary);
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    /* File preview styles */
    .file-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    .file-preview-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        min-width: 200px;
    }

    .file-preview-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .video-thumbnail {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-light);
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    .file-preview-info {
        flex: 1;
        min-width: 0;
    }

    .file-preview-name {
        display: block;
        font-weight: 500;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.875rem;
    }

    .file-preview-size {
        display: block;
        color: var(--text-muted);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .file-preview-remove {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--danger-color);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }

    .file-preview-remove:hover {
        background: var(--danger-dark);
        transform: scale(1.1);
    }

    /* Video Preview Styles */
    .video-preview-item {
        background: var(--bg-secondary);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .video-preview-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .video-preview-container {
        position: relative;
        width: 100%;
        height: 120px;
        overflow: hidden;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }

    .file-preview-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        background: #000;
    }

    .video-preview-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: rgba(255, 255, 255, 0.9);
        font-size: 2rem;
        pointer-events: none;
        opacity: 0.8;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        transition: opacity 0.3s ease;
    }

    .video-preview-container:hover .video-preview-overlay {
        opacity: 1;
    }

    /* Current files display */
    .current-files {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--bg-secondary);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
    }

    .current-files h4 {
        margin: 0 0 1rem 0;
        color: var(--text-primary);
        font-size: 1rem;
        font-weight: 600;
    }

    .current-files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .current-file-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg-primary);
        border: 1px solid var(--border-light);
        border-radius: var(--border-radius);
    }

    .current-file-preview {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .current-media-section {
        margin-bottom: 2rem;
    }

    .current-media-section h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .current-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }

    .current-media-item {
        position: relative;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .current-media-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        display: block;
    }

    /* Enhanced Video Styling */
    .video-media-item {
        background: var(--bg-secondary);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .video-media-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .video-container {
        position: relative;
        width: 100%;
        height: 120px;
        overflow: hidden;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }

    .current-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        background: #000;
    }

    .video-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: rgba(255, 255, 255, 0.9);
        font-size: 2rem;
        pointer-events: none;
        opacity: 0.8;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        transition: opacity 0.3s ease;
    }

    .video-container:hover .video-overlay {
        opacity: 1;
    }

    .video-info {
        padding: 0.75rem;
        background: var(--bg-primary);
        border-top: 1px solid var(--border-color);
    }

    .video-filename {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-secondary);
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .remove-media-checkbox {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 120px; /* Match video container height */
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 10;
    }

    .current-media-item:hover .remove-media-checkbox {
        opacity: 1;
    }

    /* Special positioning for video items */
    .video-media-item .remove-media-checkbox {
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }

    .remove-media-checkbox input[type="checkbox"] {
        display: none;
    }

    .remove-media-checkbox label {
        color: white;
        cursor: pointer;
        padding: 0.5rem 1rem;
        background: rgba(239, 68, 68, 0.8);
        border-radius: var(--radius-md);
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s ease;
    }

    .remove-media-checkbox label:hover {
        background: #ef4444;
    }

    .remove-media-checkbox input[type="checkbox"]:checked + label {
        background: #10b981;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .mobile-menu-btn {
            display: block !important;
        }

        .header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .header-actions {
            justify-content: flex-start;
        }

        .creation-info {
            grid-template-columns: 1fr;
        }

        .form-section {
            padding: 1.5rem;
        }

        .form-actions {
            padding: 1.5rem;
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }

    /* SweetAlert2 Custom Styling */
    .swal-custom-popup {
        border-radius: 16px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .swal-custom-button {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
        transition: all 0.2s ease !important;
    }

    .swal-custom-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('active');

        // Add overlay when sidebar is active
        if (sidebar.classList.contains('active')) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.background = 'rgba(0,0,0,0.5)';
            overlay.style.zIndex = '999';
            overlay.style.backdropFilter = 'blur(2px)';
            overlay.onclick = toggleSidebar;
            document.body.appendChild(overlay);
        } else {
            document.querySelector('.sidebar-overlay')?.remove();
        }
    }

    // Multiple Images Upload Preview
    let selectedImages = [];
    const maxImages = 2;
    let selectedVideos = [];
    const maxVideos = 1;
    
    // Track removed media checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox' && (e.target.name === 'remove_images[]' || e.target.name === 'remove_videos[]')) {
            updateAvailableSlots();
        }
    });
    
    function updateAvailableSlots() {
        // Get all checked removal checkboxes
        const removedImageIndexes = Array.from(document.querySelectorAll('input[name="remove_images[]"]:checked')).map(cb => cb.value);
        const removedVideoIndexes = Array.from(document.querySelectorAll('input[name="remove_videos[]"]:checked')).map(cb => cb.value);
        
        const currentImageCount = {{ count($currentImages) }};
        const currentVideoCount = {{ count($currentVideos) }};
        
        const removedImagesCount = removedImageIndexes.length;
        const removedVideosCount = removedVideoIndexes.length;
        
        // Calculate remaining slots after removal
        const remainingImages = currentImageCount - removedImagesCount;
        const remainingVideos = currentVideoCount - removedVideosCount;
        
        // Available slots = max allowed - remaining after removal
        const availableImageSlots = Math.max(0, maxImages - remainingImages);
        const availableVideoSlots = Math.max(0, maxVideos - remainingVideos);
        
        // Update upload area labels
        const imageLabel = document.querySelector('label[for="images"]');
        const videoLabel = document.querySelector('label[for="videos"]');
        
        if (imageLabel) {
            if (availableImageSlots === 0) {
                imageLabel.innerHTML = `<i class="fas fa-images"></i> Images (Limit reached - remove existing images first)`;
            } else {
                imageLabel.innerHTML = `<i class="fas fa-images"></i> Images (${availableImageSlots} available)`;
            }
        }
        
        if (videoLabel) {
            if (availableVideoSlots === 0) {
                videoLabel.innerHTML = `<i class="fas fa-video"></i> Video (Limit reached - remove existing video first)`;
            } else {
                videoLabel.innerHTML = `<i class="fas fa-video"></i> Video (${availableVideoSlots} available)`;
            }
        }
    }
    
    document.getElementById('images').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const container = document.getElementById('imagesPreview');
        
        // Calculate available slots
        const currentImageCount = {{ count($currentImages) }};
        const removedImageIndexes = Array.from(document.querySelectorAll('input[name="remove_images[]"]:checked')).map(cb => cb.value);
        const removedImagesCount = removedImageIndexes.length;
        const remainingImages = currentImageCount - removedImagesCount;
        const availableSlots = Math.max(0, maxImages - remainingImages);
        
        // Validate file count against available slots
        if (files.length > availableSlots) {
            let errorMessage;
            if (availableSlots === 0) {
                errorMessage = `You have reached the maximum limit of ${maxImages} images. Please remove existing images first before uploading new ones.`;
            } else {
                errorMessage = `You can only upload ${availableSlots} more images. You selected ${files.length} files.`;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Upload Limit Exceeded',
                text: errorMessage,
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'swal-custom-popup',
                    confirmButton: 'swal-custom-button'
                }
            });
            e.target.value = '';
            return;
        }
        
        // Clear previous previews
        container.innerHTML = '';
        selectedImages = [];
        
        files.forEach((file, index) => {
            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: `File ${file.name} is not a valid image format. Only JPG and PNG are allowed.`,
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    }
                }).then(() => {
                    e.target.value = '';
                });
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `File ${file.name} is too large. Maximum size is 5MB.`,
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    }
                }).then(() => {
                    e.target.value = '';
                });
                return;
            }
            
            selectedImages.push(file);
            
            // Create preview
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            
            const img = document.createElement('img');
            img.className = 'file-preview-thumbnail';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-preview-info';
            fileInfo.innerHTML = `
                <span class="file-preview-name">${file.name}</span>
                <span class="file-preview-size">${(file.size / 1024).toFixed(1)} KB</span>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'file-preview-remove';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function() {
                removeFilePreview(this, index, 'images');
            };
            
            previewItem.appendChild(img);
            previewItem.appendChild(fileInfo);
            previewItem.appendChild(removeBtn);
            container.appendChild(previewItem);
        });
    });

    document.getElementById('videos').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const container = document.getElementById('videosPreview');
        
        // Calculate available slots
        const currentVideoCount = {{ count($currentVideos) }};
        const removedVideoIndexes = Array.from(document.querySelectorAll('input[name="remove_videos[]"]:checked')).map(cb => cb.value);
        const removedVideosCount = removedVideoIndexes.length;
        const remainingVideos = currentVideoCount - removedVideosCount;
        const availableSlots = Math.max(0, maxVideos - remainingVideos);
        
        // Validate file count against available slots
        if (files.length > availableSlots) {
            let errorMessage;
            if (availableSlots === 0) {
                errorMessage = `You have reached the maximum limit of ${maxVideos} video. Please remove the existing video first before uploading a new one.`;
            } else {
                errorMessage = `You can only upload ${availableSlots} more video. You selected ${files.length} files.`;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Upload Limit Exceeded',
                text: errorMessage,
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'swal-custom-popup',
                    confirmButton: 'swal-custom-button'
                }
            });
            e.target.value = '';
            return;
        }
        
        // Clear previous previews
        container.innerHTML = '';
        selectedVideos = [];
        
        files.forEach((file, index) => {
            // Validate file type
            if (!['video/mp4'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: `File ${file.name} is not a valid video format. Only MP4 is allowed.`,
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    }
                }).then(() => {
                    e.target.value = '';
                });
                return;
            }
            
            // Validate file size (50MB)
            if (file.size > 50 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `File ${file.name} is too large. Maximum size is 50MB.`,
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    }
                }).then(() => {
                    e.target.value = '';
                });
                return;
            }
            
            selectedVideos.push(file);
            
            // Create preview with actual video thumbnail
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item video-preview-item';
            
            // Create video container
            const videoContainer = document.createElement('div');
            videoContainer.className = 'video-preview-container';
            
            // Create video element for thumbnail
            const videoElement = document.createElement('video');
            videoElement.className = 'file-preview-video';
            videoElement.controls = true;
            videoElement.preload = 'metadata';
            
            // Create object URL for the video file
            const videoURL = URL.createObjectURL(file);
            videoElement.src = videoURL;
            
            // Add play overlay
            const playOverlay = document.createElement('div');
            playOverlay.className = 'video-preview-overlay';
            playOverlay.innerHTML = '<i class="fas fa-play-circle"></i>';
            
            videoContainer.appendChild(videoElement);
            videoContainer.appendChild(playOverlay);
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-preview-info';
            fileInfo.innerHTML = `
                <span class="file-preview-name">${file.name}</span>
                <span class="file-preview-size">${(file.size / 1024 / 1024).toFixed(1)} MB</span>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'file-preview-remove';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function() {
                // Revoke object URL to free memory
                URL.revokeObjectURL(videoURL);
                removeFilePreview(this, index, 'videos');
            };
            
            previewItem.appendChild(videoContainer);
            previewItem.appendChild(fileInfo);
            previewItem.appendChild(removeBtn);
            container.appendChild(previewItem);
        });
    });

    function removeFilePreview(button, index, type) {
        const previewItem = button.closest('.file-preview-item');
        previewItem.remove();
        
        if (type === 'images') {
            selectedImages.splice(index, 1);
            // Update the file input
            const dt = new DataTransfer();
            selectedImages.forEach(file => dt.items.add(file));
            document.getElementById('images').files = dt.files;
        } else {
            selectedVideos.splice(index, 1);
            // Update the file input
            const dt = new DataTransfer();
            selectedVideos.forEach(file => dt.items.add(file));
            document.getElementById('videos').files = dt.files;
        }
    }

    // Initialize available slots on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAvailableSlots();
    });
</script>

@endsection
