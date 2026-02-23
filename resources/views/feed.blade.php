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

        .tabs {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .tab-btn {
            background: #334155;
            color: #94a3b8;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            background: #475569;
            color: #e2e8f0;
        }

        .tab-btn.active {
            background: #3b82f6;
            color: #fff;
        }

        .tag-filters {
            display: flex;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }

        .tag-filter-btn {
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 1rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            opacity: 0.6;
        }

        .tag-filter-btn:hover, .tag-filter-btn.active {
            opacity: 1;
        }

        .tag-filter-btn[data-tag="all"] { background: #475569; color: #e2e8f0; }
        .tag-filter-btn[data-tag="humeur"] { background: #3b82f6; color: #fff; }
        .tag-filter-btn[data-tag="question"] { background: #f97316; color: #fff; }
        .tag-filter-btn[data-tag="annonce"] { background: #ef4444; color: #fff; }
        .tag-filter-btn[data-tag="blague"] { background: #22c55e; color: #fff; }
        .tag-filter-btn[data-tag="code"] { background: #8b5cf6; color: #fff; }
        .tag-filter-btn[data-tag="random"] { background: #6b7280; color: #fff; }

        .feed {
            flex: 1;
            min-width: 0;
        }

        .post {
            background: #1e293b;
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1rem;
            border-left: 4px solid #3b82f6;
        }

        .post-new {
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

        .post-header-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .post-author {
            font-size: 1.4rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .post-tag {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .tag-humeur { background: #3b82f6; color: #fff; }
        .tag-question { background: #f97316; color: #fff; }
        .tag-annonce { background: #ef4444; color: #fff; }
        .tag-blague { background: #22c55e; color: #fff; }
        .tag-code { background: #8b5cf6; color: #fff; }
        .tag-random { background: #6b7280; color: #fff; }

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

        .post-footer {
            display: flex;
            gap: 1.25rem;
            margin-top: 0.75rem;
            color: #64748b;
            font-size: 0.95rem;
        }

        .post-stat {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .main-content {
            display: flex;
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem;
            gap: 1.5rem;
        }

        .players-panel {
            width: 250px;
            flex-shrink: 0;
        }

        .players-card {
            background: #1e293b;
            border-radius: 1rem;
            padding: 1.25rem;
            position: sticky;
            top: 6rem;
        }

        .players-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #334155;
        }

        .players-list {
            list-style: none;
        }

        .players-list li {
            padding: 0.5rem 0;
            font-size: 1rem;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .players-list li::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3b82f6;
            flex-shrink: 0;
        }

        .player-count {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.75rem;
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

        /* Annuaire */
        .annuaire-section {
            display: none;
        }

        .annuaire-section.active {
            display: block;
        }

        .feed-section {
            display: block;
        }

        .feed-section.hidden {
            display: none;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .profile-card {
            background: #1e293b;
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-pseudo {
            font-size: 1.2rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 0.25rem;
        }

        .profile-bio {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            min-height: 1.5rem;
        }

        .profile-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        .profile-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #e2e8f0;
        }

        .post-stat.clickable {
            cursor: pointer;
            transition: color 0.2s;
        }

        .post-stat.clickable:hover {
            color: #3b82f6;
        }

        .comments-section {
            margin-top: 0.75rem;
            border-top: 1px solid #334155;
            padding-top: 0.75rem;
            display: none;
        }

        .comments-section.open {
            display: block;
        }

        .comment {
            padding: 0.5rem 0;
            font-size: 0.95rem;
        }

        .comment + .comment {
            border-top: 1px solid #1e293b;
        }

        .comment-author {
            font-weight: 700;
            color: #3b82f6;
            font-size: 0.85rem;
        }

        .comment-content {
            color: #cbd5e1;
            margin-top: 0.15rem;
        }

        .comment-time {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .comments-empty {
            color: #64748b;
            font-size: 0.85rem;
            font-style: italic;
        }

        .comments-loading {
            color: #64748b;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Feed Promo</h1>
        <div class="subtitle">Réseau social de la salle</div>
        <div class="tabs">
            <button class="tab-btn active" data-tab="feed">Feed</button>
            <button class="tab-btn" data-tab="annuaire">Annuaire</button>
        </div>
        <div class="tag-filters" id="tag-filters">
            <button class="tag-filter-btn active" data-tag="all">Tous</button>
            <button class="tag-filter-btn" data-tag="humeur">Humeur</button>
            <button class="tag-filter-btn" data-tag="question">Question</button>
            <button class="tag-filter-btn" data-tag="annonce">Annonce</button>
            <button class="tag-filter-btn" data-tag="blague">Blague</button>
            <button class="tag-filter-btn" data-tag="code">Code</button>
            <button class="tag-filter-btn" data-tag="random">Random</button>
        </div>
    </div>

    <div class="main-content">
        <div class="feed-section" id="feed-section">
            <div class="feed" id="feed">
                <div class="empty-state">En attente des premiers posts...</div>
            </div>
        </div>

        <div class="annuaire-section" id="annuaire-section">
            <div class="profile-grid" id="profile-grid"></div>
        </div>

        <div class="players-panel">
            <div class="players-card">
                <h2>Inscrits</h2>
                <div class="player-count">{{ $players->count() }} membre{{ $players->count() !== 1 ? 's' : '' }}</div>
                @if($players->isEmpty())
                    <p style="color: #64748b; font-size: 0.95rem;">Aucun inscrit pour le moment.</p>
                @else
                    <ul class="players-list">
                        @foreach($players as $pseudo)
                            <li>{{ $pseudo }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="status-bar">
        <span><span class="status-dot"></span> Actualisation automatique</span>
        <span id="post-count">0 posts</span>
    </div>

    <script>
        var knownIds = new Set();
        var currentTag = 'all';
        var currentTab = 'feed';

        function timeAgo(dateString) {
            var now = new Date();
            var date = new Date(dateString);
            var seconds = Math.floor((now - date) / 1000);

            if (seconds < 10) return "à l'instant";
            if (seconds < 60) return "il y a " + seconds + " sec";

            var minutes = Math.floor(seconds / 60);
            if (minutes < 60) return "il y a " + minutes + " min";

            var hours = Math.floor(minutes / 60);
            if (hours < 24) return "il y a " + hours + "h";

            var days = Math.floor(hours / 24);
            return "il y a " + days + "j";
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function createPostElement(post, animate) {
            var div = document.createElement('div');
            div.className = 'post' + (animate ? ' post-new' : '');
            div.dataset.id = post.id;
            div.dataset.createdAt = post.created_at;

            var tagHtml = '';
            if (post.tag) {
                tagHtml = '<span class="post-tag tag-' + escapeHtml(post.tag) + '">' + escapeHtml(post.tag) + '</span>';
            }

            div.innerHTML =
                '<div class="post-header">' +
                    '<div class="post-header-left">' +
                        '<span class="post-author">@' + escapeHtml(post.author) + '</span>' +
                        tagHtml +
                    '</div>' +
                    '<span class="post-time">' + timeAgo(post.created_at) + '</span>' +
                '</div>' +
                '<div class="post-content">' + escapeHtml(post.content) + '</div>' +
                '<div class="post-footer">' +
                    '<span class="post-stat">&#10084; ' + (post.likes_count || 0) + '</span>' +
                    '<span class="post-stat clickable" onclick="toggleComments(this, ' + post.id + ')">&#128172; ' + (post.comments_count || 0) + '</span>' +
                '</div>' +
                '<div class="comments-section" data-post-id="' + post.id + '"></div>';
            return div;
        }

        function updateTimeLabels() {
            document.querySelectorAll('.post').forEach(function(el) {
                var createdAt = el.dataset.createdAt;
                if (createdAt) {
                    el.querySelector('.post-time').textContent = timeAgo(createdAt);
                }
            });
        }

        function renderFeed(posts) {
            var feed = document.getElementById('feed');
            var postCount = document.getElementById('post-count');

            postCount.textContent = posts.length + ' post' + (posts.length !== 1 ? 's' : '');

            if (posts.length === 0) {
                feed.innerHTML = '<div class="empty-state">En attente des premiers posts...</div>';
                knownIds = new Set();
                return;
            }

            var newPosts = posts.filter(function(post) {
                return !knownIds.has(post.id);
            });

            if (newPosts.length === 0) {
                updateTimeLabels();
                return;
            }

            // Premier chargement ou changement de filtre : tout afficher sans animation
            if (knownIds.size === 0) {
                feed.innerHTML = '';
                posts.forEach(function(post) {
                    knownIds.add(post.id);
                    feed.appendChild(createPostElement(post, false));
                });
                return;
            }

            // Insérer les nouveaux posts en haut avec animation
            newPosts.reverse().forEach(function(post) {
                knownIds.add(post.id);
                feed.insertBefore(createPostElement(post, true), feed.firstChild);
            });

            updateTimeLabels();
        }

        function fetchFeed() {
            var url = '/api/feed';
            if (currentTag !== 'all') {
                url += '?tag=' + encodeURIComponent(currentTag);
            }

            fetch(url)
                .then(function(response) { return response.json(); })
                .then(function(posts) { renderFeed(posts); })
                .catch(function(error) { console.error('Erreur fetch feed:', error); });
        }

        function fetchProfiles() {
            fetch('/api/profiles')
                .then(function(response) { return response.json(); })
                .then(function(profiles) { renderProfiles(profiles); })
                .catch(function(error) { console.error('Erreur fetch profiles:', error); });
        }

        function renderProfiles(profiles) {
            var grid = document.getElementById('profile-grid');

            if (profiles.length === 0) {
                grid.innerHTML = '<div class="empty-state">Aucun profil pour le moment.</div>';
                return;
            }

            grid.innerHTML = '';
            profiles.forEach(function(profile) {
                var card = document.createElement('div');
                card.className = 'profile-card';

                var avatarContent = '';
                if (profile.avatar_url) {
                    avatarContent = '<img src="' + escapeHtml(profile.avatar_url) + '" alt="avatar" onerror="this.parentNode.textContent=\'' + escapeHtml(profile.pseudo).charAt(0).toUpperCase() + '\'">';
                } else {
                    avatarContent = escapeHtml(profile.pseudo).charAt(0).toUpperCase();
                }

                card.innerHTML =
                    '<div class="profile-avatar">' + avatarContent + '</div>' +
                    '<div class="profile-pseudo">@' + escapeHtml(profile.pseudo) + '</div>' +
                    '<div class="profile-bio">' + (profile.bio ? escapeHtml(profile.bio) : 'Pas de bio') + '</div>' +
                    '<div class="profile-stats">' +
                        '<div class="profile-stat"><span class="profile-stat-value">' + (profile.posts_count || 0) + '</span>posts</div>' +
                        '<div class="profile-stat"><span class="profile-stat-value">' + (profile.likes_received || 0) + '</span>likes</div>' +
                        '<div class="profile-stat"><span class="profile-stat-value">' + (profile.followers_count || 0) + '</span>followers</div>' +
                    '</div>';
                grid.appendChild(card);
            });
        }

        // Gestion des onglets
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                currentTab = btn.dataset.tab;

                var feedSection = document.getElementById('feed-section');
                var annuaireSection = document.getElementById('annuaire-section');
                var tagFilters = document.getElementById('tag-filters');

                if (currentTab === 'feed') {
                    feedSection.classList.remove('hidden');
                    annuaireSection.classList.remove('active');
                    tagFilters.style.display = 'flex';
                } else {
                    feedSection.classList.add('hidden');
                    annuaireSection.classList.add('active');
                    tagFilters.style.display = 'none';
                    fetchProfiles();
                }
            });
        });

        // Gestion des filtres par tag
        document.querySelectorAll('.tag-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tag-filter-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                currentTag = btn.dataset.tag;
                knownIds = new Set();
                fetchFeed();
            });
        });

        function toggleComments(el, postId) {
            var section = el.closest('.post').querySelector('.comments-section');

            if (section.classList.contains('open')) {
                section.classList.remove('open');
                return;
            }

            section.innerHTML = '<div class="comments-loading">Chargement...</div>';
            section.classList.add('open');

            fetch('/api/posts/' + postId + '/comments')
                .then(function(response) { return response.json(); })
                .then(function(comments) {
                    if (comments.length === 0) {
                        section.innerHTML = '<div class="comments-empty">Aucun commentaire.</div>';
                        return;
                    }

                    section.innerHTML = '';
                    comments.forEach(function(comment) {
                        var commentDiv = document.createElement('div');
                        commentDiv.className = 'comment';
                        commentDiv.innerHTML =
                            '<span class="comment-author">@' + escapeHtml(comment.author) + '</span>' +
                            '<div class="comment-content">' + escapeHtml(comment.content) + '</div>' +
                            '<div class="comment-time">' + timeAgo(comment.created_at) + '</div>';
                        section.appendChild(commentDiv);
                    });
                })
                .catch(function() {
                    section.innerHTML = '<div class="comments-empty">Erreur de chargement.</div>';
                });
        }

        fetchFeed();
        setInterval(fetchFeed, 5000);
    </script>
</body>
</html>
