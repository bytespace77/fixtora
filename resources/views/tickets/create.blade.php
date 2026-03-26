@extends('layouts.app')

@section('title', 'Create Ticket - Fixtora')

@section('content')
<div class="ticket-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Create New Ticket</h1>
            <p class="subtitle">Submit a new support ticket and we'll get back to you shortly.</p>
        </div>
    </div>

    <div class="ticket-form-wrapper">
        <!-- Left Column - Form -->
        <div class="ticket-form-main">
            <form method="POST" action="{{ route('tickets.store') }}" class="ticket-form">
                @csrf

                <!-- Issue Identity Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <h3>Issue Identity</h3>
                        <p>Tell us what you need help with</p>
                    </div>

                    <div class="form-group">
                        <label for="title">Ticket Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            required
                            placeholder="Brief summary of your issue"
                            class="form-input"
                        >
                        @error('title')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="system">System/Component *</label>
                            <select id="system" name="system" required class="form-input">
                                <option value="">Select a system...</option>
                                <option value="api">API Services</option>
                                <option value="database">Database</option>
                                <option value="auth">Authentication</option>
                                <option value="ui">UI/Frontend</option>
                                <option value="infrastructure">Infrastructure</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority *</label>
                            <select id="priority" name="priority" required class="form-input">
                                <option value="">Select priority...</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6" 
                            required
                            placeholder="Provide detailed information about the issue..."
                            class="form-input"
                        ></textarea>
                        @error('description')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Attachments Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <h3>Attachments</h3>
                        <p>Add screenshots or files to help us understand better</p>
                    </div>

                    <div class="dropzone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p>Drag and drop files here or click to browse</p>
                        <span>Max file size: 10MB</span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('home') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Create Ticket</button>
                </div>
            </form>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="ticket-sidebar">
            <!-- Impact Assessment -->
            <div class="sidebar-card">
                <h4>Impact Assessment</h4>
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="impact" value="low" checked>
                        <span class="radio-label">
                            <strong>Low</strong>
                            <small>Affects single user</small>
                        </span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="impact" value="medium">
                        <span class="radio-label">
                            <strong>Medium</strong>
                            <small>Affects team/department</small>
                        </span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="impact" value="high">
                        <span class="radio-label">
                            <strong>High</strong>
                            <small>Affects entire system</small>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-card">
                <h4>Quick Actions</h4>
                <button class="action-btn">📋 Use Template</button>
                <button class="action-btn">👥 Add Watcher</button>
                <button class="action-btn">🏷️ Add Labels</button>
            </div>

            <!-- Help -->
            <div class="sidebar-card info">
                <h4>💡 Tips</h4>
                <ul>
                    <li>Be as specific as possible</li>
                    <li>Include error messages</li>
                    <li>Attach screenshots</li>
                    <li>Describe steps to reproduce</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style scoped>
    .ticket-container {
        padding: 32px 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 32px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .subtitle {
        color: var(--text-secondary);
        margin: 0;
        font-size: 14px;
    }

    .ticket-form-wrapper {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .ticket-form-wrapper {
            grid-template-columns: 1fr;
        }
    }

    /* Form Card */
    .form-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
    }

    .form-card-header {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-card-header h3 {
        margin: 0 0 4px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .form-card-header p {
        margin: 0;
        font-size: 13px;
        color: var(--text-secondary);
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--text-primary);
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Montserrat', sans-serif;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    textarea.form-input {
        resize: vertical;
        min-height: 120px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .error {
        color: var(--danger);
        font-size: 13px;
        margin-top: 4px;
    }

    /* Dropzone */
    .dropzone {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--bg-light);
    }

    .dropzone:hover {
        border-color: var(--primary-light);
        background: rgba(37, 99, 235, 0.05);
    }

    .dropzone svg {
        width: 40px;
        height: 40px;
        color: var(--primary-light);
        margin-bottom: 12px;
        stroke-width: 2;
    }

    .dropzone p {
        margin: 0 0 4px 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .dropzone span {
        display: block;
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .btn-cancel,
    .btn-submit {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        font-family: 'Montserrat', sans-serif;
        transition: all 0.2s;
    }

    .btn-cancel {
        background: var(--bg-light);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-cancel:hover {
        background: white;
    }

    .btn-submit {
        background: var(--primary-light);
        color: white;
    }

    .btn-submit:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
    }

    /* Sidebar */
    .ticket-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sidebar-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 16px;
        box-shadow: var(--shadow);
    }

    .sidebar-card.info {
        background: var(--bg-light);
        border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .sidebar-card h4 {
        margin: 0 0 12px 0;
        font-size: 14px;
        font-weight: 600;
    }

    /* Radio Group */
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .radio-item:hover {
        background: var(--bg-light);
    }

    .radio-item input[type="radio"] {
        margin-right: 10px;
        cursor: pointer;
    }

    .radio-label {
        display: flex;
        flex-direction: column;
    }

    .radio-label strong {
        font-size: 13px;
    }

    .radio-label small {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    /* Action Buttons */
    .action-btn {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        background: white;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        margin-bottom: 8px;
        transition: all 0.2s;
        font-family: 'Montserrat', sans-serif;
    }

    .action-btn:hover {
        background: var(--bg-light);
        border-color: var(--primary-light);
    }

    /* Help List */
    .sidebar-card.info ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .sidebar-card.info li {
        margin-bottom: 6px;
    }
</style>
@endsection