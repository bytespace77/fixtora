@extends('layouts.app')

@section('title', 'Request Custom Integration – Fixtora')

@section('styles')
<style>
.page-title { margin-bottom:18px; }
.page-title h1 { font-size:22px; font-weight:800; letter-spacing:-.5px; color:var(--navy); }
.page-title p { font-size:13px; color:var(--muted); margin-top:4px; line-height:1.6; }

.card-box {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:20px;
  box-shadow:var(--shadow);
}

label.lbl{display:block;font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-input{
  width:100%;
  padding:10px 12px;
  border:1px solid var(--border);
  border-radius:8px;
  font-size:13px;
  font-family:inherit;
  outline:none;
  color:var(--text);
  background:var(--surface);
}
.form-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,78,216,.08)}
.error-msg{font-size:11px;color:var(--red);margin-top:4px;font-weight:600}

textarea.form-input{min-height:120px;resize:vertical}

.btn-full{
  width:100%;
  padding:11px;
  border-radius:8px;
  font-size:13px;
  font-weight:800;
  font-family:inherit;
  cursor:pointer;
  border:none;
  background:var(--blue);
  color:#fff;
}
.btn-full:hover{background:#1a42c4}
</style>
@endsection

@section('content')
<div class="page-title">
  <h1>Request Custom Integration</h1>
  <p>Tell us which system you want to connect and how it should behave. We’ll review your request and reach out soon.</p>
</div>

<div class="card-box">
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('integrations.custom-request.store') }}">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div>
        <label class="lbl" for="requested_integration">Integration Name</label>
        <input
          id="requested_integration"
          type="text"
          name="requested_integration"
          class="form-input {{ $errors->has('requested_integration') ? 'is-invalid' : '' }}"
          value="{{ old('requested_integration') }}"
          placeholder="e.g. Salesforce / ServiceNow"
          required
        />
        @error('requested_integration')
          <div class="error-msg">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <label class="lbl" for="contact_name">Contact Name</label>
        <input
          id="contact_name"
          type="text"
          name="contact_name"
          class="form-input {{ $errors->has('contact_name') ? 'is-invalid' : '' }}"
          value="{{ old('contact_name') }}"
          placeholder="Your name"
          required
        />
        @error('contact_name')
          <div class="error-msg">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;">
      <div>
        <label class="lbl" for="email">Email (Optional)</label>
        <input
          id="email"
          type="email"
          name="email"
          class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
          value="{{ old('email') }}"
          placeholder="email@company.com"
        />
        @error('email')
          <div class="error-msg">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <label class="lbl" for="company">Company (Optional)</label>
        <input
          id="company"
          type="text"
          name="company"
          class="form-input {{ $errors->has('company') ? 'is-invalid' : '' }}"
          value="{{ old('company') }}"
          placeholder="Company name"
        />
        @error('company')
          <div class="error-msg">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div style="margin-top:14px;">
      <label class="lbl" for="message">What do you want to integrate? (Optional)</label>
      <textarea
        id="message"
        name="message"
        class="form-input {{ $errors->has('message') ? 'is-invalid' : '' }}"
        placeholder="Describe the workflows, events, or data you want to sync."
      >{{ old('message') }}</textarea>
      @error('message')
        <div class="error-msg">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-top:16px;">
      <button class="btn-full" type="submit">Submit Request</button>
    </div>
  </form>
</div>
@endsection

