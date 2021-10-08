/**
 * @author Greenrivers
 * @copyright Copyright (c) 2021 Greenrivers
 */

import $ from 'jquery';

export default function progressbar() {
    const convert = $('#convert');
    const result = $('.conversion-field .result');
    const progressbar = $('.conversion-field #progressbar');
    let total_images = 0;

    const convertImages = () => {
        $.ajax({
            url: '/admin/plugins/webp/convert',
            async: false,
            type: 'POST'
        }).done(data => {
            const {converted_images} = data;
            let width = Math.round(
                parseInt(progressbar.css('width')) / parseInt(progressbar.parent().css('width')) * 100
            ) + Math.round(100 / total_images);

            if (converted_images === total_images) {
                width = 100;
            }

            progressbar.css('width', `${width}%`);
            progressbar.html(`${width}%`);
            convert.prop('disabled', true);
            result.html(`Converted ${converted_images}/${total_images} images.`);

            if (converted_images < total_images) {
                convertImages();
            } else {
                convert.prop('disabled', false);
            }
        });
    }

    convert.on('click', () => {
        $.ajax({
            url: '/admin/plugins/webp/images',
            type: 'GET'
        }).done(data => {
            total_images = data.total_images;

            if (total_images > 0) {
                progressbar.css('width', `0%`);
                progressbar.html(`0%`);

                convertImages();
            } else {
                result.html('No images to conversion.');
            }
        }).fail(data => {
            console.error(data);
        })
    });
}
