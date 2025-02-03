<?php require_once 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видеохостинг ДТК</title>
    <link rel="stylesheet" href="/styles/video.css">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <style>
        .notification {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 20px;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .notification.success {
            background-color: #4caf50;
        }

        .notification.error {
            background-color: #f44336;
        }

        /* Стили для кнопки удаления на десктопах */
        .video-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            opacity: 0; /* По умолчанию скрываем */
            transition: opacity 0.3s ease; /* Плавное появление */
        }

        .video-card:hover .video-actions {
            opacity: 1; /* Показываем при наведении */
        }

        /* Стили для кнопки удаления на мобильных устройствах */
        @media (max-width: 768px) {
            .video-actions {
                opacity: 1; /* Всегда показываем на мобильных */
            }
        }

        .video-actions .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }

        .video-actions .action-btn img {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>

    <!-- Шапка -->
    <header class="header">
        <div class="header__logo">
            <a href="index.html">
                <img src="/img/logosvgvideo.svg" alt="Логотип" class="logo-img">
            </a>
        </div>
        
        <div class="header__search">
            <input type="text" class="search-input" placeholder="Поиск">
        </div>
        
        <div class="header__actions">
            <button class="action-btn" id="uploadBtn">
                <img src="/img/addvideosvg.svg" alt="Добавить видео">
            </button>
            <button class="action-btn" id="logoutBtn" onclick="location.href='/index.html'">
                <img src="/img/exitsvg.svg" alt="Выход">
            </button>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="main-content">
        <div class="video-grid" id="videoGrid">
            <!-- Видео будет загружаться сюда -->
        </div>
    </main>

    <!-- Модальное окно загрузки -->
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <img src="/img/uploadsvg.svg" class="upload-icon">
            <h2>Выберите файл для загрузки</h2>
            <input type="file" id="videoFile" accept="video/mp4,video/webm,video/ogg">
            <button class="upload-button" onclick="uploadVideo()">Начать загрузку</button>
            <div class="progress-container">
                <div class="progress-bar"></div>
                <span class="progress-text">0%</span>
            </div>
            <p id="uploadMessage"></p>
        </div>
    </div>

    <!-- Видеоплеер -->
    <div class="video-player-overlay" id="videoPlayer">
        <div class="video-player-container">
            <button class="close-player">&times;</button>
            <video controls class="main-video">
                <source src="" type="video/mp4">
            </video>
        </div>
    </div>

    <!-- Уведомление -->
    <div id="notification" class="notification"></div>

<script>
    // Открытие/закрытие модалки
    document.getElementById('uploadBtn').addEventListener('click', () => {
        document.getElementById('uploadModal').classList.add('active');
    });

    document.querySelector('.close-modal').addEventListener('click', () => {
        document.getElementById('uploadModal').classList.remove('active');
    });

    // Функция для генерации превью
    function generatePreview(videoElement, videoCard) {
        const videoThumbnail = videoCard.querySelector('.video-thumbnail');
        
        // Если экран <=768px, устанавливаем заглушку
        if (window.innerWidth <= 768) {
            console.log("Используем заглушку, экран <=768px");
            videoThumbnail.style.cssText = "background: url(/img/zaglushka.png) no-repeat center/cover;";
            return;
        }
        
        // Для экранов >768px генерируем превью из видео
        videoElement.muted = true;
        videoElement.setAttribute('playsinline', 'true');
        const canvas = document.createElement('canvas');
        canvas.width = 320;
        canvas.height = 180;
        const ctx = canvas.getContext('2d');
        
        videoElement.currentTime = 5;
        videoElement.addEventListener('seeked', function onSeeked() {
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            const thumbnail = canvas.toDataURL('image/jpeg');
            videoThumbnail.style.cssText = `background: url(${thumbnail}) no-repeat center/cover;`;
            videoElement.removeEventListener('seeked', onSeeked);
        });
    }

    function uploadVideo() {
        const fileInput = document.getElementById('videoFile');
        const file = fileInput.files[0];
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.querySelector('.progress-text');
        const uploadMessage = document.getElementById('uploadMessage');
        const progressContainer = document.querySelector('.progress-container');

        uploadMessage.textContent = ''; // Очищаем предыдущие сообщения
        progressContainer.style.display = 'block'; // Показываем прогресс-бар

        if (!file) {
            uploadMessage.textContent = 'Выберите файл!';
            progressContainer.style.display = 'none'; // Скрываем прогресс-бар, если файл не выбран
            return;
        }

        let formData = new FormData();
        formData.append('video', file);

        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);

        xhr.upload.onprogress = function(event) {
            let percent = Math.round((event.loaded / event.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                uploadMessage.textContent = 'Файл успешно загружен!';
                loadVideos(); // Обновляем список видео
            } else {
                uploadMessage.textContent = 'Ошибка: ' + xhr.responseText;
            }
            progressContainer.style.display = 'none'; // Скрываем прогресс-бар после завершения
        };

        xhr.onerror = function() {
            uploadMessage.textContent = 'Ошибка загрузки файла!';
            console.error('Ошибка загрузки файла');
            progressContainer.style.display = 'none'; // Скрываем прогресс-бар при ошибке
        };

        xhr.send(formData);
    }

    // Загрузка видео из БД
    function loadVideos() {
        fetch('get_videos.php')
        .then(response => response.json())
        .then(data => {
            const videoGrid = document.getElementById('videoGrid');
            videoGrid.innerHTML = '';
            data.forEach(video => {
                const videoCard = document.createElement('div');
                videoCard.classList.add('video-card');
                videoCard.dataset.videoUrl = video.video_path;
                videoCard.style.display = 'block'; // Убедитесь, что карточка видима по умолчанию

                const videoElement = document.createElement('video');
                videoElement.src = video.video_path;
                videoElement.crossOrigin = "anonymous";

                videoElement.addEventListener('loadeddata', () => {
                    generatePreview(videoElement, videoCard);
                });

                videoCard.addEventListener('click', () => {
                    const videoPlayer = document.getElementById('videoPlayer');
                    videoPlayer.querySelector('.main-video source').src = video.video_path;
                    videoPlayer.querySelector('.main-video').load();
                    videoPlayer.classList.add('active');
                });

                videoCard.innerHTML = `
                <div class="video-thumbnail">
                    <div class="video-date">${video.upload_date}</div>
                    <div class="play-icon">
                        <svg viewBox="0 0 24 24">
                            <path fill="#ff9900" d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="video-info">
                    <h3 class="video-title">${video.title || 'Загруженное видео'}</h3>
                    <div class="video-meta">
                        <span class="video-link" onclick="event.stopPropagation(); copyToClipboard('${video.video_path}')">${video.video_path}</span>
                    </div>
                </div>
                <div class="video-actions">
                    <button class="action-btn" onclick="event.stopPropagation(); deleteVideo(${video.video_id}, '${video.video_path}')">
                        <img src="/img/dots.svg" alt="Удалить">
                    </button>
                </div>
            `;
                videoGrid.appendChild(videoCard);
            });
        });
    }

    // Функция удаления видео
    function deleteVideo(videoId, videoPath) {
        if (confirm('Вы уверены, что хотите удалить это видео?')) {
            fetch('delete_video.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ video_id: videoId, video_path: videoPath })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Видео успешно удалено', true);
                    loadVideos(); // Обновляем список видео
                } else {
                    showNotification('Ошибка при удалении видео', false);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('Ошибка при удалении видео', false);
            });
        }
    }

    // Функция копирования ссылки в буфер обмена с уведомлением
    function copyToClipboard(text) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        // Показ уведомления
        showNotification('Ссылка скопирована в буфер обмена', true);
    }

    // Функция для показа уведомлений
    function showNotification(message, isSuccess) {
        const notification = document.getElementById("notification");
        notification.textContent = message;
        notification.className = isSuccess ? "notification success" : "notification error";
        notification.style.display = "block";

        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);
    }

    // Закрытие видеоплеера
    document.querySelector('.close-player').addEventListener('click', () => {
    const videoPlayer = document.getElementById('videoPlayer');
    const mainVideo = videoPlayer.querySelector('.main-video');
    mainVideo.pause();          // Останавливаем воспроизведение
    mainVideo.currentTime = 0;  // Сбрасываем время воспроизведения к началу
    videoPlayer.classList.remove('active'); // Скрываем плеер
    });

    // Обработчик поиска
    document.querySelector('.search-input').addEventListener('input', function(event) {
        const searchQuery = event.target.value.toLowerCase(); // Получаем значение поиска и приводим к нижнему регистру
        filterVideos(searchQuery); // Фильтруем видео
    });

    // Функция фильтрации видео
    function filterVideos(searchQuery) {
        const videoCards = document.querySelectorAll('.video-card'); // Получаем все карточки видео

        videoCards.forEach(videoCard => {
            const videoTitle = videoCard.querySelector('.video-title').textContent.toLowerCase(); // Получаем заголовок видео и приводим к нижнему регистру

            if (videoTitle.includes(searchQuery)) {
                videoCard.style.display = 'block'; // Показываем карточку, если заголовок совпадает с запросом
            } else {
                videoCard.style.display = 'none'; // Скрываем карточку, если заголовок не совпадает
            }
        });
    }

    // Загружаем видео при загрузке страницы
    loadVideos();
</script>

</body>
</html>