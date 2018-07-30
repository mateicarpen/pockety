var persistence = {
    apiPrefix: '/api/v1',

    getArticleList: function(callback) {
        this.makeRequest('GET', '/articles', callback);
    },

    archiveArticle: function(article, callback) {
        this.makeRequest('POST', '/articles/archive', callback, article);
    },

    passArticle: function(article, callback) {
        this.makeRequest('POST', '/articles/pass', callback, article);
    },

    tagArticle: function(article, tagId, callback) {
        this.makeRequest('POST', '/articles/tag', callback, {
            id: article.id,
            tag_id: tagId,
            timestamp: article.timestamp
        });
    },

    resetArticles: function(callback) {
        this.makeRequest('POST', '/articles/reset', callback);
    },

    getTagList: function(callback) {
        this.makeRequest('GET', '/tags', callback);
    },

    createTag: function(tag, callback) {
        this.makeRequest('POST', '/tags', callback, tag);
    },

    deleteTag: function(id, callback) {
        this.makeRequest('DELETE', '/tags/' + id, callback);
    },

    makeRequest: function(method, url, callback, data) {
        $.ajax({
            url: this.apiPrefix + url,
            data: data,
            type: method,
            success: callback
        });
    }
};

new Vue({
    el: '#app',

    data: {
        articles: [],
        currentIndex: 0,
        tags: [],
        newTag: {},
        loading: true
    },

    ready: function() {
        this.retrieveArticles();
        this.retrieveTags();
    },

    computed: {
        currentArticle: function() {
            if (!this.articles.length) {
                return {};
            }

            return this.articles[this.currentIndex];
        }
    },

    methods: {
        retrieveArticles: function() {
            this.loading = true;

            persistence.getArticleList(function(data) {
                this.articles = data.list;
                this.currentIndex = 0;
                this.loading = false;
            }.bind(this));
        },

        retrieveTags: function() {
            persistence.getTagList(function(data) {
                this.tags = data;
            }.bind(this));
        },

        archiveArticle: function() {
            persistence.archiveArticle(this.currentArticle);

            this.nextArticle();
        },

        passArticle: function() {
            persistence.passArticle(this.currentArticle);

            this.nextArticle();
        },

        addTag: function(tag) {
            persistence.tagArticle(this.currentArticle, tag.id);

            this.nextArticle();
        },

        reset: function() {
            if (!confirm('Are you sure you want to return to the oldest article? This cannot be undone.')) {
                return;
            }

            this.loading = true;

            persistence.resetArticles(function(){
                window.location.reload();
            });
        },

        createTag: function() {
            persistence.createTag(this.newTag, function(data) {
                this.tags.push(data);
                this.newTag = {};
            }.bind(this));
        },

        deleteTag: function(tag) {
            if (!confirm('Are you sure you want to delete this tag?')) {
                return;
            }

            persistence.deleteTag(tag.id, function() {
                this.tags.$remove(tag);
            }.bind(this));
        },

        nextArticle: function() {
            //don't go over the number of articles
            if (this.articles.length > this.currentIndex + 1) {
                this.currentIndex++;
            } else {
                this.articles = [];
            }
        }
    }
});