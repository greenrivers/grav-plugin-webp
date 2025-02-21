/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 */

import $ from 'jquery';

export default function progressbar() {
    const {base_url_relative = '/admin'} = window.GravAdmin.config;

    const originalPath = $('input[name="data[original_path]"]');
    const quality = $('#quality');

    const convert = $('#convert');
    const result = $('.conversion-field .result');
    const progressbar = $('.conversion-field #progressbar');

    let total_images = 0;

    originalPath.on('click', () => {
        setConfig()
            .then((data) => {
                console.log(data);
            })
            .catch((error) => {
                console.error(error)
            })
    })

    convert.on('click', () => {
        $.ajax({
            url: `${base_url_relative}/plugins/webp/images`,
            type: 'GET'
        }).done(data => {
            total_images = data.total_images;

            if (total_images > 0) {
                progressbar.css('width', `0%`);
                progressbar.html(`0%`);

                setConfig()
                    .then((data) => {
                        const {original_path, quality} = data;
                        if (original_path && quality) {
                            convertImages();
                        }
                    })
                    .catch((error) => {
                        console.error(error)
                    })
            } else {
                result.html('No images to conversion.');
            }
        }).fail(data => {
            console.error(data);
        })
    });

    const setConfig = () => {
        const checkedOriginalPath = $('input[name="data[original_path]"]:checked');
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${base_url_relative}/plugins/webp/set_config`,
                async: false,
                type: 'POST',
                data: {
                    original_path: checkedOriginalPath.val(),
                    quality: quality.val()
                },
                success: (data) => {
                    resolve(data);
                },
                error: (error) => {
                    reject(error);
                }
            });
        });
    }

    const convertImages = () => {
        $.ajax({
            url: `${base_url_relative}/plugins/webp/convert`,
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
}
