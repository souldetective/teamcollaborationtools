(function($){
    const repeaterSelector = '.atbs-links-repeater';

    function initColorPickers() {
        $('.atbs-color-field').wpColorPicker();
    }

    function addRow($repeater, column) {
        const $rows = $repeater.find('.atbs-links-rows');
        const index = $rows.find('.atbs-link-row').length;
        const optionKey = 'theme_branding_settings';
        const row = `
            <div class="atbs-link-row" data-index="${index}">
                <input type="text" class="regular-text" name="${optionKey}[footer][${column}][${index}][label]" placeholder="${ATBSBranding.addLink}" />
                <input type="url" class="regular-text" name="${optionKey}[footer][${column}][${index}][url]" placeholder="https://" />
                <button type="button" class="button atbs-move-up" aria-label="${ATBSBranding.moveUp}">&#8593;</button>
                <button type="button" class="button atbs-move-down" aria-label="${ATBSBranding.moveDown}">&#8595;</button>
                <button type="button" class="button atbs-remove-row">&times;</button>
            </div>`;
        $rows.append(row);
    }

    function renumber($repeater) {
        $repeater.find('.atbs-link-row').each(function(i){
            $(this).attr('data-index', i);
            $(this).find('input').each(function(){
                const name = $(this).attr('name');
                if (!name) return;
                const newName = name.replace(/\[\d+\]/, '['+i+']');
                $(this).attr('name', newName);
            });
        });
    }

    function bindRepeaterEvents() {
        $(document).on('click', '.atbs-add-link', function(){
            const column = $(this).data('column');
            const $repeater = $(this).closest(repeaterSelector);
            addRow($repeater, column);
        });

        $(document).on('click', '.atbs-remove-row', function(){
            const $repeater = $(this).closest(repeaterSelector);
            $(this).closest('.atbs-link-row').remove();
            renumber($repeater);
        });

        $(document).on('click', '.atbs-move-up', function(){
            const $row = $(this).closest('.atbs-link-row');
            const $prev = $row.prev();
            if ($prev.length) {
                $row.insertBefore($prev);
                renumber($row.closest(repeaterSelector));
            }
        });

        $(document).on('click', '.atbs-move-down', function(){
            const $row = $(this).closest('.atbs-link-row');
            const $next = $row.next();
            if ($next.length) {
                $row.insertAfter($next);
                renumber($row.closest(repeaterSelector));
            }
        });
    }

    function initMediaField() {
        $(document).on('click', '.atbs-upload-media', function(e){
            e.preventDefault();
            const $wrapper = $(this).closest('.atbs-media-field');
            const $input = $wrapper.find('.atbs-media-input');
            const frame = wp.media({
                title: ATBSBranding.upload,
                button: { text: ATBSBranding.upload },
                multiple: false
            });
            frame.on('select', function(){
                const attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.id);
                $wrapper.find('.preview').html(`<img src="${attachment.url}" style="max-width:120px;height:auto;" />`);
            });
            frame.open();
        });

        $(document).on('click', '.atbs-remove-media', function(e){
            e.preventDefault();
            const $wrapper = $(this).closest('.atbs-media-field');
            $wrapper.find('.atbs-media-input').val('');
            $wrapper.find('.preview').html('<span class="placeholder">'+ATBSBranding.remove+'</span>');
        });
    }

    $(function(){
        initColorPickers();
        bindRepeaterEvents();
        initMediaField();
    });
})(jQuery);
