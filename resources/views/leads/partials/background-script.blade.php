<script>
(function () {
    const baseUrl = @json(route('leads.section', [$lead, 'SECTION']));
    const pending = {}, completed = {};
    function url(section) { return baseUrl.replace('SECTION', section); }
    function fillOptions(selector, rows, label) {
        $(selector).each(function () {
            const selected = $(this).val();
            $(this).empty().append(new Option(label, ''));
            rows.forEach(row => this.add(new Option(row.name || row.country_name, row.id)));
            $(this).val(selected).trigger('change.select2');
        });
    }
    const targets = {
        sidebar: ['#lead-tasks-list', '#lead-contacts-list', '#lead-opportunities-list', '#lead-tasks-tab-list'],
        notes: ['#lead-notes-list'], files: ['#lead-files-list'], address: []
    };
    function loadSection(section) {
        if (completed[section]) return Promise.resolve();
        if (pending[section]) return pending[section];
        pending[section] = fetch(url(section), {credentials: 'same-origin', headers: {'Accept': 'application/json'}})
            .then(response => { if (!response.ok) throw new Error('Section request failed'); return response.json(); })
            .then(data => {
                if (section === 'sidebar') {
                    $('#lead-tasks-list, #lead-tasks-tab-list').html(data.tasks);
                    $('#lead-contacts-list').html(data.contacts);
                    $('#lead-opportunities-list').html(data.opportunities);
                    fillOptions('#assigned_to, #o_assigned_to', data.users, 'Select User');
                    fillOptions('#lead_contact_id', data.contact_options, 'Select Contact');
                } else if (section === 'address') {
                    // Preserve the current country even if it falls outside today's dropdown scope.
                    const selected = $('#country_id').val(), text = $('#country_id option:selected').text();
                    fillOptions('#country_id', data.countries, 'Select Country');
                    if (selected && !data.countries.some(row => String(row.id) === String(selected))) {
                        $('#country_id').append(new Option(text, selected, true, true));
                    }
                } else {
                    $(targets[section][0]).html(data.html);
                }
                completed[section] = true;
            }).catch(() => {
                (targets[section] || []).forEach(target => {
                    const button = $('<button type="button" class="btn btn-link btn-sm">Could not load. Retry</button>');
                    button.on('click', () => loadSection(section));
                    $(target).empty().append(button);
                });
                if (section === 'address') {
                    $('#lead-address-retry').remove();
                    $('<button type="button" id="lead-address-retry" class="btn btn-link btn-sm">Reload countries</button>')
                        .on('click', function () { $(this).remove(); loadSection('address'); }).insertAfter('#country_id');
                }
            }).finally(() => { delete pending[section]; });
        return pending[section];
    }
    window.initLeadPincode = function () {
        const field = $('#pincode_id');
        if (field.hasClass('select2-hidden-accessible')) field.select2('destroy');
        field.select2({
            dropdownParent: $('#AddAddressModel'), width: '100%',
            ajax: {
                url: url('pincodes'), dataType: 'json', delay: 250,
                data: params => ({q: params.term || '', page: params.page || 1, city_id: $('#city_id').val()}),
                processResults: data => data
            }
        });
    };
    window.resetLeadPincode = function () {
        $('#pincode_id').empty().append(new Option('Select Pincode', '')).trigger('change.select2');
    };
    let editorPromise;
    window.ensureLeadNoteEditor = function () {
        if (!editorPromise) {
            editorPromise = new Promise((resolve, reject) => {
                function initialize() {
                    if (!CKEDITOR.instances.note_text) {
                        const editor = CKEDITOR.replace('note_text', {customConfig: 'config.js', toolbar: 'Basic', height: '15em'});
                        editor.on('change', function () { this.updateElement(); });
                        editor.on('instanceReady', resolve);
                    } else resolve();
                }
                if (window.CKEDITOR) return initialize();
                const script = document.createElement('script');
                script.src = @json(asset('vendor/ckeditor/js/ckeditor.js'));
                script.onload = initialize;
                script.onerror = reject;
                document.head.appendChild(script);
            }).catch(() => { editorPromise = null; }); // Plain textarea remains usable on download failure.
        }
        return editorPromise;
    };
    document.addEventListener('DOMContentLoaded', function () {
        // Let the browser paint the activity page before starting nonessential requests.
        requestAnimationFrame(() => requestAnimationFrame(() => {
            loadSection('sidebar').then(() => loadSection('address'));
            loadSection('notes').then(() => loadSection('files'));
        }));
        $('#myTab a').on('shown.bs.tab', function () {
            const tab = $(this).attr('href');
            if (tab === '#notes' || tab === '#files') loadSection(tab.substring(1));
            if (tab === '#tasks') loadSection('sidebar');
        });
        $('#AddAddressModel').on('show.bs.modal', () => loadSection('address'));
        $('#frmLeadNotesAdd').on('submit', function () {
            if (window.CKEDITOR && CKEDITOR.instances.note_text) CKEDITOR.instances.note_text.updateElement();
        });
        $('#lead-more-activity').on('click', function () {
            const button = $(this), page = Number(button.attr('data-page'));
            button.prop('disabled', true).text('Loading activities…');
            fetch(url('activity') + '?page=' + page, {credentials: 'same-origin', headers: {'Accept': 'application/json'}})
                .then(response => { if (!response.ok) throw new Error('Activity request failed'); return response.json(); })
                .then(data => {
                    $('#lead-activity-items').append(data.html);
                    button.attr('data-page', page + 1).toggle(data.has_more).text('More activities');
                }).catch(() => button.text('Retry more activities'))
                .finally(() => button.prop('disabled', false));
        });
    });
})();
</script>
