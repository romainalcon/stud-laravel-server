<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed Promo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }

        .header {
            background: #1e293b;
            border-bottom: 2px solid #3b82f6;
            padding: 1.5rem 2rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header .subtitle {
            color: #94a3b8;
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        .feed {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .post {
            background: #1e293b;
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1rem;
            border-left: 4px solid #3b82f6;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .post-author {
            font-size: 1.4rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .post-time {
            font-size: 0.95rem;
            color: #64748b;
        }

        .post-content {
            font-size: 1.6rem;
            line-height: 1.5;
            color: #f1f5f9;
            word-break: break-word;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
            font-size: 1.5rem;
        }

        .status-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            border-top: 1px solid #334155;
            padding: 0.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Feed Promo</h1>
        <div class="subtitle">Réseau social de la salle</div>
    </div>

    <div class="feed" id="feed">
        <div class="empty-state">En attente des premiers posts...</div>
    </div>

    <div class="status-bar">
        <span><span class="status-dot"></span> Actualisation automatique</span>
        <span id="post-count">0 posts</span>
    </div>

    <script>
        function timeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 10) return "à l'instant";
            if (seconds < 60) return "il y a " + seconds + " sec";

            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return "il y a " + minutes + " min";

            const hours = Math.floor(minutes / 60);
            if (hours < 24) return "il y a " + hours + "h";

            const days = Math.floor(hours / 24);
            return "il y a " + days + "j";
        }

        function renderFeed(posts) {
            const feed = document.getElementById('feed');
            const postCount = document.getElementById('post-count');

            postCount.textContent = posts.length + ' post' + (posts.length !== 1 ? 's' : '');

            if (posts.length === 0) {
                feed.innerHTML = '<div class="empty-state">En attente des premiers posts...</div>';
                return;
            }

            feed.innerHTML = posts.map(function(post) {
                return '<div class="post">' +
                    '<div class="post-header">' +
                        '<span class="post-author">@' + escapeHtml(post.author) + '</span>' +
                        '<span class="post-time">' + timeAgo(post.created_at) + '</span>' +
                    '</div>' +
                    '<div class="post-content">' + escapeHtml(post.content) + '</div>' +
                '</div>';
            }).join('');
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function fetchFeed() {
            fetch('/api/feed')
                .then(function(response) { return response.json(); })
                .then(function(posts) { renderFeed(posts); })
                .catch(function(error) { console.error('Erreur fetch feed:', error); });
        }

        fetchFeed();
        setInterval(fetchFeed, 5000);
    </script>
</body>
</html>
