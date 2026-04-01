@extends('layouts.app')
@section('title', 'New Ticket – Fixtora')

@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--muted);margin-bottom:8px}
.breadcrumb a{color:var(--muted);text-decoration:none}.breadcrumb a:hover{color:var(--blue)}
.sep{color:var(--border-dark)}
.current{color:var(--text-sub)}
.page-header{margin-bottom:24px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}

.form-grid{display:grid;grid-template-columns:1fr 280px;gap:18px}
.form-col{display:flex;flex-direction:column;gap:14px}

/* CARD BOX */
.card-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow)}
.card-box-title{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;margin-bottom:16px;color:var(--navy)}
.card-box-icon{width:28px;height:28px;background:var(--blue-bg);border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0}
.card-box-icon.red{background:#fee2e2;color:var(--red)}

/* FORM FIELDS */
label.lbl{display:block;font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-input{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;color:var(--text);background:var(--surface);transition:border-color .12s}
.form-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,78,216,.08)}
.form-input.is-invalid{border-color:var(--red)}
.error-msg{font-size:11px;color:var(--red);margin-top:4px;font-weight:600}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.field-group{margin-bottom:12px}
.field-group:last-child{margin-bottom:0}

/* TEXTAREA EDITOR */
.editor-wrap{border:1px solid var(--border);border-radius:8px;overflow:hidden}
.editor-wrap:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,78,216,.08)}
.editor-toolbar{display:flex;gap:2px;padding:6px 8px;background:var(--bg);border-bottom:1px solid var(--border)}
.tb-btn{padding:4px 8px;border:none;background:transparent;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit;color:var(--text-sub);border-radius:4px;transition:background .1s}
.tb-btn:hover{background:var(--border)}
textarea.form-input{border:none;border-radius:0;min-height:120px;resize:vertical}
textarea.form-input:focus{border:none;box-shadow:none}

/* UPLOAD */
.upload-zone{border:2px dashed var(--border-dark);border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:all .15s}
.upload-zone:hover{border-color:var(--blue);background:var(--blue-bg)}
.upload-icon{font-size:22px;margin-bottom:8px}
.upload-title{font-size:13px;font-weight:600;color:var(--text-sub);margin-bottom:4px}
.upload-sub{font-size:11px;color:var(--muted)}
.upload-link{display:inline-block;margin-top:10px;font-size:11px;font-weight:700;letter-spacing:.6px;color:var(--blue)}

/* IMPACT RADIO */
.impact-option{display:flex;align-items:flex-start;gap:10px;padding:10px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;margin-bottom:6px;transition:all .12s}
.impact-option:last-child{margin-bottom:0}
.impact-option input[type=radio]{margin-top:2px;flex-shrink:0}
.impact-option:has(input:checked){border-color:var(--blue);background:var(--blue-bg)}
.impact-option.critical:has(input:checked){border-color:var(--red);background:#fee2e2}
.impact-option.high:has(input:checked){border-color:var(--orange);background:var(--orange-bg)}
.impact-label{font-size:12.5px;font-weight:700}
.impact-desc{font-size:11px;color:var(--muted);margin-top:1px}

/* ACTIONS */
.btn-full{width:100%;padding:11px;border-radius:8px;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .15s;text-decoration:none;margin-bottom:8px}
.btn-submit{background:var(--blue);color:#fff;border:none}
.btn-submit:hover{background:#1a42c4}
.btn-outline-full{background:transparent;color:var(--text-sub);border:1px solid var(--border)}
.btn-outline-full:hover{background:var(--bg)}
.btn-cancel{background:transparent;color:var(--muted);border:1px solid var(--border)}
.btn-cancel:hover{background:var(--bg)}

/* SYSTEM HEALTH */
.health-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)}
.health-row:last-child{border-bottom:none}
.health-name{font-size:12.5px;font-weight:600}
.health-badge{font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px}
.stable{background:#dcfce7;color:#15803d}
.degraded{background:#fee2e2;color:var(--red)}
</style>
@endsection

@section('content')
<div class="page-header">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('tickets.index') }}">Tickets</a>
    <span class="sep">/</span>
    <span class="current">New Ticket</span>
  </div>
  <h1>Create New Service Request</h1>
</div>

<form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="form-grid">
  <!-- LEFT COLUMN -->
  <div class="form-col">

    <!-- Issue Identity -->
    <div class="card-box">
      <div class="card-box-title">
        <div class="card-box-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        Issue Identity
      </div>

      <div class="field-group">
        <label class="lbl" for="title">Issue Title</label>
        <input type="text" id="title" name="title" class="form-input {{ $errors->has('title') ? 'is-invalid' : '' }}" value="{{ old('title') }}" placeholder="e.g., Performance degradation in API v2"/>
        @error('title')<div class="error-msg">{{ $message }}</div>@enderror
      </div>

      <div class="field-row">
        @if(auth()->user()->isSuperAdmin())
        <div class="field-group">
          <label class="lbl" for="system">System (Company)</label>
          <select id="system" name="system" class="form-input {{ $errors->has('system') ? 'is-invalid' : '' }}">
            <option value="">Select Company System</option>
            @foreach($companies as $companyName)
            <option value="{{ $companyName }}" {{ old('system') == $companyName ? 'selected' : '' }}>{{ $companyName }}</option>
            @endforeach
          </select>
          @error('system')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        @else
        <div class="field-group">
          <label class="lbl" for="system">System</label>
          @if(!empty($companySystems))
            <select id="system" name="system" class="form-input {{ $errors->has('system') ? 'is-invalid' : '' }}">
              <option value="">Select System</option>
              @foreach($companySystems as $sys)
                <option value="{{ $sys }}" {{ old('system') == $sys ? 'selected' : '' }}>{{ $sys }}</option>
              @endforeach
            </select>
            @error('system')<div class="error-msg">{{ $message }}</div>@enderror
          @else
            <input type="text" class="form-input" value="{{ auth()->user()->company->name ?? 'Unknown Company' }}" disabled />
            <input type="hidden" name="system" value="{{ auth()->user()->company->name ?? 'Unknown Company' }}" />
          @endif
        </div>
        @endif
        <div class="field-group">
          <label class="lbl" for="priority">Priority</label>
          <select id="priority" name="priority" class="form-input {{ $errors->has('priority') ? 'is-invalid' : '' }}">
            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
            <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
          </select>
          @error('priority')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="field-row">
        <div class="field-group">
          <label class="lbl" for="status">Ticket Status</label>
          <select id="status" name="status" class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
            <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="in_review" {{ old('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
            <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
          </select>
          @error('status')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="field-group">
          <label class="lbl" for="due_date">Due Date</label>
          <input type="date" id="due_date" name="due_date" class="form-input {{ $errors->has('due_date') ? 'is-invalid' : '' }}" value="{{ old('due_date') }}" />
          @error('due_date')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    <!-- Functional Details -->
    <div class="card-box">
      <div class="card-box-title">
        <div class="card-box-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/></svg>
        </div>
        Functional Details
      </div>
      <label class="lbl" for="description">Detailed Description</label>
      <div class="editor-wrap {{ $errors->has('description') ? 'is-invalid' : '' }}" style="{{ $errors->has('description') ? 'border-color:var(--red)' : '' }}">
        <div class="editor-toolbar">
          <button type="button" class="tb-btn" style="font-weight:900">B</button>
          <button type="button" class="tb-btn" style="font-style:italic">I</button>
          <button type="button" class="tb-btn" style="font-family:monospace">&lt;/&gt;</button>
        </div>
        <textarea id="description" name="description" class="form-input" placeholder="Please describe the architectural or functional issue in detail…">{{ old('description') }}</textarea>
      </div>
      @error('description')<div class="error-msg">{{ $message }}</div>@enderror
    </div>

    <!-- Evidentiary Support -->
    <div class="card-box">
      <div class="card-box-title">
        <div class="card-box-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </div>
        Evidentiary Support
      </div>
      <label for="file_upload" style="display:block;cursor:pointer;">
        <div class="upload-zone" id="dropzone">
          <div class="upload-icon">📎</div>
          <div class="upload-title" id="dropzone-title">Drag and drop log files or screenshots here</div>
          <div class="upload-sub">Maximum file size 25MB · JPG, PNG, LOG, JSON, ZIP</div>
          <span class="upload-link">OR BROWSE FILES</span>
        </div>
      </label>
      <input type="file" id="file_upload" name="attachments[]" multiple
             accept=".jpg,.jpeg,.png,.log,.json,.zip"
             style="display:none;"
             onchange="handleFileSelect(this.files)">
      <div id="file-list" style="margin-top:8px;font-size:12px;line-height:1.8;"></div>
    </div>

  </div>

  <!-- RIGHT COLUMN -->
  <div class="form-col">

    <!-- Impact Level -->
    <div class="card-box">
      <div class="card-box-title">
        <div class="card-box-icon red">!</div>
        Impact Level
      </div>

      <label class="impact-option critical">
        <input type="radio" name="impact" value="critical" {{ old('impact') == 'critical' ? 'checked' : '' }}/>
        <div>
          <div class="impact-label" style="color:var(--red)">Critical</div>
          <div class="impact-desc">System-wide outage, blocking operations</div>
        </div>
      </label>
      <label class="impact-option high">
        <input type="radio" name="impact" value="high" {{ old('impact') == 'high' ? 'checked' : '' }}/>
        <div>
          <div class="impact-label" style="color:var(--orange)">High</div>
          <div class="impact-desc">Significant impact, workarounds difficult</div>
        </div>
      </label>
      <label class="impact-option">
        <input type="radio" name="impact" value="medium" {{ old('impact', 'medium') == 'medium' ? 'checked' : '' }}/>
        <div>
          <div class="impact-label" style="color:var(--blue)">Medium</div>
          <div class="impact-desc">Partial degradation, workarounds available</div>
        </div>
      </label>
      <label class="impact-option">
        <input type="radio" name="impact" value="low" {{ old('impact') == 'low' ? 'checked' : '' }}/>
        <div>
          <div class="impact-label" style="color:var(--muted)">Low</div>
          <div class="impact-desc">Minor annoyance or cosmetic issue</div>
        </div>
      </label>
      @error('impact')<div class="error-msg" style="margin-top:8px">{{ $message }}</div>@enderror
    </div>

    <!-- Actions -->
    <div class="card-box">
      <div style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--navy)">Submission Actions</div>
      <button type="submit" class="btn-full btn-submit">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Submit Ticket
      </button>
      <button type="button" class="btn-full btn-outline-full">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
        Save as Draft
      </button>
      <a href="{{ route('tickets.index') }}" class="btn-full btn-cancel" style="color:var(--muted)">Cancel &amp; Discard</a>
    </div>

    <!-- System Health -->
    <div class="card-box">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div style="font-size:13px;font-weight:700;color:var(--navy)">System Health</div>
        <div style="width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse 2s infinite"></div>
      </div>
      <div class="health-row"><span class="health-name">CRM Portal</span><span class="health-badge stable">Stable</span></div>
      <div class="health-row"><span class="health-name">Payment GW</span><span class="health-badge stable">Stable</span></div>
      <div class="health-row"><span class="health-name">API v2</span><span class="health-badge degraded">Degraded</span></div>
      <div class="health-row"><span class="health-name">Auth Service</span><span class="health-badge stable">Stable</span></div>
    </div>

  </div>
</div>
</form>

<style>
.file-pill{display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:6px 10px;font-size:12px;font-weight:600;color:#15803d;margin-bottom:5px;}
.file-pill.error{background:#fef2f2;border-color:#fecaca;color:#dc2626;}
.file-pill-name{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.file-pill-size{font-size:11px;color:#94a3b8;flex-shrink:0;}
.file-pill-remove{background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px;line-height:1;padding:0 2px;border-radius:4px;transition:color .12s;flex-shrink:0;}
.file-pill-remove:hover{color:#dc2626;}
</style>
<script>
  // Shared file store for create page
  let selectedFiles = [];
  const maxMB = 25;
  const allowed = ['jpg','jpeg','png','log','json','zip'];

  const dz = document.getElementById('dropzone');
  dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='#2563eb'; dz.style.background='var(--blue-bg)'; });
  dz.addEventListener('dragleave', () => { dz.style.borderColor=''; dz.style.background=''; });
  dz.addEventListener('drop', e => {
    e.preventDefault(); dz.style.borderColor=''; dz.style.background='';
    addFiles([...e.dataTransfer.files]);
  });

  function handleFileSelect(files) { addFiles([...files]); }

  function addFiles(incoming) {
    incoming.forEach(f => {
      // avoid duplicates by name
      if (!selectedFiles.find(x => x.name === f.name)) {
        selectedFiles.push(f);
      }
    });
    syncInputAndRender();
  }

  function removeFile(name) {
    selectedFiles = selectedFiles.filter(f => f.name !== name);
    syncInputAndRender();
  }

  function syncInputAndRender() {
    // Push valid files back into the real <input> via DataTransfer
    const input = document.getElementById('file_upload');
    const dt = new DataTransfer();
    selectedFiles.forEach(f => { const ext = f.name.split('.').pop().toLowerCase(); if (allowed.includes(ext) && f.size/1024/1024 <= maxMB) dt.items.add(f); });
    input.files = dt.files;

    const title = document.getElementById('dropzone-title');
    const list  = document.getElementById('file-list');
    const validCount = dt.files.length;

    title.textContent = validCount
      ? `${validCount} file(s) ready to upload`
      : 'Drag and drop log files or screenshots here';

    if (!selectedFiles.length) { list.innerHTML = ''; return; }

    list.innerHTML = selectedFiles.map(f => {
      const ext    = f.name.split('.').pop().toLowerCase();
      const sizeMB = f.size / 1024 / 1024;
      const valid  = allowed.includes(ext) && sizeMB <= maxMB;
      const errMsg = !allowed.includes(ext) ? ' — unsupported format' : ` — exceeds 25MB (${sizeMB.toFixed(1)}MB)`;
      const icon   = valid ? '✅' : '❌';
      const sizeLabel = valid ? `${sizeMB.toFixed(2)}MB` : '';
      return `<div class="file-pill ${valid ? '' : 'error'}">
        <span>${icon}</span>
        <span class="file-pill-name" title="${f.name}">${f.name}${!valid ? errMsg : ''}</span>
        ${valid ? `<span class="file-pill-size">${sizeLabel}</span>` : ''}
        <button type="button" class="file-pill-remove" onclick="removeFile('${f.name.replace(/'/g,"\\'")}')" title="Remove">✕</button>
      </div>`;
    }).join('');
  }
</script>
@endsection