/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 */

import $ from 'jquery';

export default function sliderRange() {
    const saveBtn = $('button.button[name="task"]');

    const quality = $('#quality');
    const value = $('.quality-field .result .value');

    const setQuality = () => {
        $.ajax({
            url: '/admin/plugins/webp/quality',
            async: false,
            type: 'POST',
            data: {quality: quality.val()}
        }).done(data => {
            console.info(`Quality: ${data.status}`);
        }).fail(data => {
            console.error(data);
        });
    }

    saveBtn.on('click', () => {
        setQuality();
    });

    quality.on('input', () => {
        value.html(quality.val());
    });
}
