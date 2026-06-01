@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <h3>Term Subjects Wizard</h3>
      <p class="text-muted mb-1">Quickly set marks for all subjects per term without re-adding subjects. This page does not modify existing forms or behavior.</p>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Class</label>
          <select id="class_id" class="form-select">
            <option value="">Select class</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}">{{ $c->className }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label d-block">Terms</label>
          <div id="terms_box" class="d-flex flex-wrap gap-2">
            <!-- dynamic -->
          </div>
          <div class="form-text">Select terms to include in the grid. Default shows all available terms.</div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12 d-flex flex-wrap align-items-end gap-2">
          <button id="btnLoad" class="btn btn-primary">Load</button>

          <div class="d-flex align-items-end gap-2">
            <div>
              <label class="form-label mb-0 small">Copy from</label>
              <select id="copyFromTerm" class="form-select form-select-sm" style="min-width:160px;"></select>
            </div>
            <div>
              <label class="form-label mb-0 small">to</label>
              <select id="copyToTerm" class="form-select form-select-sm" style="min-width:160px;"></select>
            </div>
            <button id="btnCopy" class="btn btn-outline-primary btn-sm" disabled>Copy marks to target term</button>
          </div>

          <button id="btnAuto50" class="btn btn-outline-secondary" disabled>Auto-fill passing = 50% of total</button>
          <button id="btnSave" class="btn btn-success" disabled>Save All</button>
          <div id="busy" class="ms-auto" style="display:none;">
            <span class="spinner-border spinner-border-sm"></span> Working...
          </div>
        </div>
      </div>

      <div class="table-responsive mt-3">
        <table id="grid" class="table table-bordered align-middle">
          <thead id="grid_head">
            <!-- dynamic -->
          </thead>
          <tbody id="grid_body">
            <!-- dynamic -->
          </tbody>
        </table>
      </div>

      <div id="alert_box" class="mt-2"></div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  let state = { terms: [], rows: [], selectedTerms: new Set() };

  function notify(type, msg){
    const box = document.getElementById('alert_box');
    box.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
  }

  function setBusy(v){ document.getElementById('busy').style.display = v ? '' : 'none'; }

  function renderTerms(terms){
    const box = document.getElementById('terms_box');
    box.innerHTML = '';
    terms.forEach(t => {
      const id = 'term_' + btoa(t.name).replace(/=/g,'');
      const wrapper = document.createElement('div');
      wrapper.className = 'form-check form-check-inline';
      wrapper.innerHTML = `
        <input class="form-check-input term_chk" type="checkbox" id="${id}" data-name="${t.name}" checked>
        <label class="form-check-label" for="${id}">${t.label}</label>
      `;
      box.appendChild(wrapper);
      state.selectedTerms.add(t.name);
    });
    box.insertAdjacentHTML('afterbegin', `
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="term_all" checked>
        <label class="form-check-label" for="term_all"><strong>All</strong></label>
      </div>
    `);
    box.querySelector('#term_all').addEventListener('change', (e) => {
      const on = e.target.checked;
      box.querySelectorAll('.term_chk').forEach(ch => { ch.checked = on; });
      state.selectedTerms = new Set(on ? terms.map(x=>x.name) : []);
      renderGrid();
    });
    box.querySelectorAll('.term_chk').forEach(ch => ch.addEventListener('change', (e) => {
      const name = e.target.getAttribute('data-name');
      if (e.target.checked) state.selectedTerms.add(name); else state.selectedTerms.delete(name);
      const all = document.getElementById('term_all');
      if (state.selectedTerms.size === terms.length) all.checked = true; else all.checked = false;
      renderGrid();
    }));
  }

  function renderGrid(){
    const head = document.getElementById('grid_head');
    const body = document.getElementById('grid_body');
    const terms = state.terms.filter(t => state.selectedTerms.has(t.name));

    // Header
    let th = '<tr><th style="min-width:220px;">Subject</th>';
    terms.forEach(t => {
      th += `<th class="text-center" colspan="3">${t.label}</th>`;
    });
    th += '</tr>';
    if (terms.length){
      th += '<tr><th></th>' + terms.map(_=>'<th class="text-center">Inc</th><th class="text-center">Total</th><th class="text-center">Passing</th>').join('') + '</tr>';
    }
    head.innerHTML = th;

    // Body
    let rowsHtml = '';
    state.rows.forEach(row => {
      let tr = `<tr data-id="${row.subject_id}"><td><strong>${row.subject_name}</strong>${row.subject_code?`<div class=\"text-muted small\">${row.subject_code}</div>`:''}</td>`;
      terms.forEach(t => {
        const tv = row.terms[t.name] || { total_marks: row.base_total_marks, passing_marks: row.base_passing_marks, enabled: true };
        const checked = tv.enabled === false ? '' : 'checked';
        tr += `
          <td class="text-center align-middle"><input type="checkbox" class="form-check-input include" data-term="${t.name}" ${checked}></td>
          <td><input type="number" step="0.001" min="0.001" class="form-control form-control-sm total" data-term="${t.name}" value="${tv.total_marks}"></td>
          <td><input type="number" step="0.001" min="0.001" class="form-control form-control-sm pass" data-term="${t.name}" value="${tv.passing_marks}"></td>
        `;
      });
      tr += '</tr>';
      rowsHtml += tr;
    });
    body.innerHTML = rowsHtml;

    document.getElementById('btnAuto50').disabled = state.rows.length === 0 || terms.length === 0;
    document.getElementById('btnSave').disabled = state.rows.length === 0 || terms.length === 0;
  }

  function populateCopySelects(){
    const fromSel = document.getElementById('copyFromTerm');
    const toSel = document.getElementById('copyToTerm');
    const opts = state.terms.map(t => `<option value="${t.name}">${t.label}</option>`).join('');
    fromSel.innerHTML = `<option value="">Select term...</option>` + opts;
    toSel.innerHTML = `<option value="">Select term...</option>` + opts;
    document.getElementById('btnCopy').disabled = state.terms.length < 2;
  }

  async function loadData(){
    const classId = document.getElementById('class_id').value;
    if (!classId){ notify('warning', 'Please select a class.'); return; }
    setBusy(true);
    try {
      const res = await fetch(`{{ route('term-subjects.fetch') }}?class_id=${classId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      if (!res.ok){ throw new Error('Failed to fetch data'); }
      const data = await res.json();
      state.terms = data.terms || [];
      state.rows = data.rows || [];
      state.selectedTerms = new Set(state.terms.map(t=>t.name));
      renderTerms(state.terms);
      populateCopySelects();
      renderGrid();
    } catch (e){
      notify('danger', 'Error loading: ' + e.message);
    } finally { setBusy(false); }
  }

  async function saveAll(){
    const classId = document.getElementById('class_id').value;
    if (!classId){ notify('warning', 'Please select a class.'); return; }
    const terms = [...state.selectedTerms];
    if (terms.length === 0){ notify('warning', 'Please select at least one term.'); return; }

    // Build payload
    const rows = [];
    document.querySelectorAll('#grid_body tr').forEach(tr => {
      const subjectId = parseInt(tr.getAttribute('data-id'));
      const termMap = {};
      tr.querySelectorAll('input.total').forEach(inp => {
        const t = inp.getAttribute('data-term');
        const total = parseFloat(inp.value || '0');
        termMap[t] = termMap[t] || {};
        termMap[t].total_marks = total;
      });
      tr.querySelectorAll('input.include').forEach(inp => {
        const t = inp.getAttribute('data-term');
        termMap[t] = termMap[t] || {};
        termMap[t].enabled = inp.checked ? 1 : 0;
      });
      tr.querySelectorAll('input.pass').forEach(inp => {
        const t = inp.getAttribute('data-term');
        const pass = parseFloat(inp.value || '0');
        termMap[t] = termMap[t] || {};
        termMap[t].passing_marks = pass;
      });
      rows.push({ subject_id: subjectId, terms: termMap });
    });

    setBusy(true);
    console.log('Sending save request:', { class_id: parseInt(classId), rows });
    
    try {
      const res = await fetch(`{{ route('term-subjects.save') }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ class_id: parseInt(classId), rows })
      });
      
      console.log('Response status:', res.status, res.statusText);
      const data = await res.json();
      console.log('Response data:', data);
      
      if (!res.ok) {
        if (data.errors) {
          console.error('Validation errors:', data.errors);
          const errorMessages = Object.values(data.errors).flat().join(', ');
          throw new Error('Validation errors: ' + errorMessages);
        }
        throw new Error(data.error || `HTTP ${res.status}: ${res.statusText}`);
      }
      
      if (!data.success) {
        throw new Error(data.error || 'Unknown error occurred');
      }
      
      const message = data.updated_count ? 
        `Saved successfully! Updated ${data.updated_count} subjects.` : 
        'Saved successfully!';
      notify('success', message);
    } catch (e){
      console.error('Save error:', e);
      notify('danger', 'Save failed: ' + e.message);
    } finally { setBusy(false); }
  }

  function doCopy(){
    const from = document.getElementById('copyFromTerm').value;
    const to = document.getElementById('copyToTerm').value;
    if (!from || !to){ notify('warning', 'Please select both source and target terms.'); return; }
    if (from === to){ notify('warning', 'Source and target terms must be different.'); return; }

    // Ensure both terms are visible in grid
    let needsRender = false;
    if (!state.selectedTerms.has(from)) { state.selectedTerms.add(from); needsRender = true; }
    if (!state.selectedTerms.has(to)) { state.selectedTerms.add(to); needsRender = true; }
    if (needsRender) { renderGrid(); }

    // Copy for each row
    let changed = 0;
    document.querySelectorAll('#grid_body tr').forEach(tr => {
      const fromTotal = tr.querySelector(`input.total[data-term="${from}"]`);
      const fromPass = tr.querySelector(`input.pass[data-term="${from}"]`);
      const toTotal = tr.querySelector(`input.total[data-term="${to}"]`);
      const toPass = tr.querySelector(`input.pass[data-term="${to}"]`);
      if (fromTotal && fromPass && toTotal && toPass){
        toTotal.value = fromTotal.value;
        toPass.value = fromPass.value;
        changed++;
      }
    });
    notify('success', `Copied marks for ${changed} subjects from "${from}" to "${to}".`);
  }

  function auto50(){
    document.querySelectorAll('#grid_body tr').forEach(tr => {
      tr.querySelectorAll('input.total').forEach(inp => {
        const total = parseFloat(inp.value || '0');
        const term = inp.getAttribute('data-term');
        const passInput = tr.querySelector(`input.pass[data-term="${term}"]`);
        if (passInput){ passInput.value = (Math.round((total * 0.5) * 1000) / 1000).toFixed(2); }
      });
    });
  }

  document.getElementById('btnLoad').addEventListener('click', loadData);
  document.getElementById('btnSave').addEventListener('click', saveAll);
  document.getElementById('btnAuto50').addEventListener('click', auto50);
  document.getElementById('btnCopy').addEventListener('click', doCopy);
  document.getElementById('class_id').addEventListener('change', loadData);
})();
</script>
@endsection
