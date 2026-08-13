{{-- admin/users/edit.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Users','href'=>route('admin.users.index')],['label'=>'Edit: '.$user->name]]"/>
    <h1 class="ds-page-title">Edit User</h1>
    <p class="ds-page-subtitle">Updating: {{ $user->name }} &middot; {{ $user->email }}</p>
  </div>
</div>

<div style="max-width:620px;">
  <form method="POST" action="{{ route('admin.users.update',$user) }}" novalidate>
    @csrf @method('PUT')

    {{-- Profile Info --}}
    <div class="ds-section" data-aos="fade-up">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-person me-2"></i>Profile Information</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="Full Name" required :error="$errors->first('name')">
          <x-ui.input type="text" name="name" :value="old('name',$user->name)" required :error="$errors->has('name')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Email Address" required :error="$errors->first('email')">
          <x-ui.input type="email" name="email" :value="old('email',$user->email)" required :error="$errors->has('email')"/>
        </x-ui.form-field>
        <x-ui.form-field label="Institution / Affiliation">
          <div class="position-relative">
            <x-ui.input type="text" name="affiliation" id="affiliation" :value="old('affiliation',$user->affiliation)" placeholder="University / Research Institute" autocomplete="off"/>
            <input type="hidden" name="institution_id" id="institution_id" value="{{ old('institution_id', $user->institution_id) }}">
            <div id="affiliation-dropdown" class="dropdown-menu w-100 shadow border mt-1" style="display:none; max-height:250px; overflow-y:auto; z-index:1000; position:absolute; background:white;"></div>
          </div>
        </x-ui.form-field>
        <div class="row g-3">
          <div class="col-md-6">
            <x-ui.form-field label="Role" required :error="$errors->first('role')">
              <x-ui.select name="role" required :error="$errors->has('role')">
                @foreach($roles as $r)
                  <option value="{{ $r }}" {{ old('role',$user->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
              </x-ui.select>
            </x-ui.form-field>
          </div>
          <div class="col-md-6">
            <x-ui.form-field label="Account Status">
              <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:var(--bg-app);border:1px solid var(--border);border-radius:var(--radius-sm);">
                <input type="hidden" name="is_active" value="0"/>
                <x-ui.checkbox id="ia" name="is_active" value="1" :checked="old('is_active',$user->is_active)" label="Account Active"/>
              </div>
            </x-ui.form-field>
          </div>
        </div>
      </div>
    </div>

    {{-- Password --}}
    <div class="ds-section" data-aos="fade-up" data-aos-delay="200">
      <div class="ds-section-hdr">
        <h3 class="ds-section-title"><i class="bi bi-key me-2"></i>Change Password</h3>
      </div>
      <div class="ds-section-body">
        <x-ui.form-field label="New Password" hint="Leave blank to keep current password." :error="$errors->first('password')">
          <x-ui.input type="password" name="password" placeholder="Min. 8 characters" :error="$errors->has('password')"/>
        </x-ui.form-field>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:4px;">
      <button type="submit" class="ds-btn ds-btn-pri">
        <i class="bi bi-check-lg"></i> Save Changes
      </button>
      <a href="{{ route('admin.users.index') }}" class="ds-btn ds-btn-out">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  initInstitutionAutocomplete('affiliation', 'institution_id', 'affiliation-dropdown');

  function initInstitutionAutocomplete(inputId, hiddenId, dropdownId) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const dropdown = document.getElementById(dropdownId);
    
    if (!input || !hidden || !dropdown) return;
    
    let debounceTimer;
    let currentFocus = -1;
    
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        hidden.value = '';
        
        if (query.length < 2) {
            closeDropdown();
            return;
        }
        
        debounceTimer = setTimeout(() => {
            fetch(`/api/v1/institutions?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    renderDropdown(data);
                })
                .catch(err => console.error('Error fetching institutions:', err));
        }, 200);
    });
    
    input.addEventListener('focus', function() {
        const query = this.value.trim();
        if (query.length >= 2 && dropdown.children.length > 0) {
            openDropdown();
        }
    });
    
    input.addEventListener('keydown', function(e) {
        let items = dropdown.getElementsByClassName('dropdown-item');
        if (e.key === 'ArrowDown') {
            currentFocus++;
            addActive(items);
            e.preventDefault();
        } else if (e.key === 'ArrowUp') {
            currentFocus--;
            addActive(items);
            e.preventDefault();
        } else if (e.key === 'Enter') {
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
                e.preventDefault();
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
        }
    });
    
    document.addEventListener('click', function(e) {
        if (e.target !== input && e.target !== dropdown && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });
    
    function renderDropdown(items) {
        dropdown.innerHTML = '';
        currentFocus = -1;
        
        if (items.length === 0) {
            closeDropdown();
            return;
        }
        
        items.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dropdown-item d-flex align-items-center justify-content-between text-start py-2 px-3';
            btn.style.border = 'none';
            btn.style.background = 'none';
            btn.style.width = '100%';
            btn.style.fontSize = '14px';
            
            let label = item.name;
            if (item.acronym) label += ` (${item.acronym})`;
            
            let meta = item.city ? `<span class="text-muted small ms-2">${item.city}</span>` : '';
            
            btn.innerHTML = `<span>${label}</span>${meta}`;
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                input.value = item.name;
                hidden.value = item.id;
                closeDropdown();
            });
            
            dropdown.appendChild(btn);
        });
        
        openDropdown();
    }
    
    function openDropdown() {
        dropdown.style.display = 'block';
    }
    
    function closeDropdown() {
        dropdown.style.display = 'none';
        currentFocus = -1;
    }
    
    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add('active');
        items[currentFocus].style.background = '#f1f5f9';
        items[currentFocus].style.color = '#0f172a';
        items[currentFocus].scrollIntoView({ block: 'nearest' });
    }
    
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('active');
            items[i].style.background = 'none';
            items[i].style.color = '';
        }
    }
  }
});
</script>
@endpush

@endsection
