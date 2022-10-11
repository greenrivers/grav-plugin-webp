/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 */

import $ from 'jquery';

export default function progressbar() {
    const quality = $('#quality');

    const convert = $('#convert');
    const result = $('.conversion-field .result');
    const progressbar = $('.conversion-field #progressbar');
    let total_images = 0;

    const setQuality = () => {
        $.ajax({
            url: '/admin/plugins/webp/quality',
            async: false,
            type: 'POST',
            data: {quality: quality.val()}
        }).done(data => {
            if (data.status) {
                convertImages();
            }
        }).fail(data => {
            console.error(data);
        });
    }

    const convertImages = () => {
        $.ajax({
            url: '/admin/plugins/webp/convert',
            type: 'POST'
        }).then((data) => {
            const {converted_images} = data;
            let width = (parseFloat(progressbar.css('width')) / parseFloat(progressbar.parent().css('width')) * 100) +
                (100 / total_images);
            let progress = Math.round(width);

            if (converted_images === total_images) {
                width = 100;
                progress = 100;
            }

            progressbar.css('width', `${width}%`);
            progressbar.html(`${progress}%`);
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

                setQuality();
            } else {
                result.html('No images to conversion.');
            }
        }).fail(data => {
            console.error(data);
        })
    });
}
