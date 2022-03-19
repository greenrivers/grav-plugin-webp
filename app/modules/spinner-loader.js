/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 */

import $ from 'jquery';

export default function spinnerLoader() {
    const clearAll = $('#clear_all');
    const result = $('.clear-all-field .result');
    const spinner = $('.clear-all-field .spinner');
    const loader = $('.clear-all-field .spinner #loading_text');

    const progressColor = '#4caf50';
    const bgColor = '#ccc';

    let webp_images = 0;

    const clearAllImages = () => {
        $.ajax({
            url: '/admin/plugins/webp/clear_all',
            type: 'POST'
        }).then((data) => {
            const {removed_images} = data;
            const value = Math.round((removed_images / webp_images) * 100);

            spinner.css('--progress-color-25', value >= 25 ? progressColor : bgColor);
            spinner.css('--progress-color-50', value >= 50 ? progressColor : bgColor);
            spinner.css('--progress-color-75', value >= 75 ? progressColor : bgColor);
            spinner.css('--progress-color-100', value >= 100 ? progressColor : bgColor);

            loader.html(`${value}%`);
            clearAll.prop('disabled', true);
            result.html(`Removed ${removed_images}/${webp_images} webp images.`);

            if (removed_images < webp_images) {
                clearAllImages();
            } else {
                clearAll.prop('disabled', false);
            }
        });
    }

    clearAll.on('click', () => {
        $.ajax({
            url: '/admin/plugins/webp/webp_images',
            type: 'GET'
        }).done((data) => {
            webp_images = data.webp_images;

            if (webp_images > 0) {
                clearAllImages();
            } else {
                result.html('No images to clear.');
            }
        }).fail((data) => {
            console.error(data);
        })
    });
}
