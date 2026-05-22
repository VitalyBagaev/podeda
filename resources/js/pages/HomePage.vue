<template>
  <div class="home-page">
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">Сервисы ИТ ОМК для сотрудников</h1>
          <p class="hero-subtitle">Управляйте вашими услугами, оставляйте отзывы и получайте поддержку в одном месте.</p>
          <div class="hero-actions">
            <a href="#" class="btn btn-primary" @click.prevent="scrollToCategories">Начать работу</a>
            <a href="#" class="btn btn-secondary" @click.prevent="scrollToComments">Посмотреть отзывы</a>
          </div>
        </div>
        <img src="/images/hero_bg.png" alt="Background" class="hero-bg-img" />
      </div>
    </section>

    <div class="container py-12">
      <section id="categories" class="mb-16">
        <div class="flex-align-center mb-8">
          <h2 class="flex-grow">Ваши категории услуг</h2>
        </div>

        <div v-if="!userInfo.id" class="alert alert-info text-center py-8">
          <p>Пожалуйста, <a href="#" @click.prevent="changePage('LoginPage')" class="link-red">войдите</a>, чтобы увидеть ваши категории услуг.</p>
        </div>
        <div v-else-if="visibleCategories.length === 0" class="card text-center py-12">
          <p class="mb-4">У вас пока нет добавленных категорий услуг.</p>
          <a href="#" class="btn btn-primary" @click.prevent="changePage('ProfilePage')">Добавить услуги в профиле</a>
        </div>
        <div v-else class="service-grid-compact">
          <div 
            v-for="cat in visibleCategories" 
            :key="cat.id" 
            class="service-card-compact"
            :class="{ active: selectedCategory === cat.id }"
            @click="selectCategory(cat.id)"
          >
            <h3 class="service-name-small">{{ cat.name }}</h3>
          </div>
        </div>
      </section>

      <section id="comments">
        <div class="card mb-12">
          <h2 class="mb-6">Оставить отзыв</h2>
          
          <div v-if="!userInfo.id" class="alert alert-danger">
            Чтобы оставить отзыв, пожалуйста, <a href="#" @click.prevent="changePage('LoginPage')" class="link-red">войдите</a> или <a href="#" @click.prevent="changePage('RegisterPage')" class="link-red">зарегистрируйтесь</a>.
          </div>

          <form v-else action="?" method="post">
            <div v-if="successMessage" class="alert alert-success">
              {{ successMessage }}
            </div>
            <div class="form-group">
              <label>Категория обращения</label>
              <div class="selected-category-field" :class="{ empty: !newComment.category_id }">
                {{ newComment.category_id ? getCategoryName(newComment.category_id) : 'Выберите категорию выше' }}
              </div>
              <div class="alert alert-danger" v-if="commentErrors && commentErrors.category_id">
                {{ commentErrors.category_id.join('. ') }}
              </div>
            </div>
            <div class="form-group">
              <label>Текст сообщения</label>
              <textarea v-model="newComment.text" rows="4" placeholder="Опишите ваше предложение или проблему..."></textarea>
              <div class="alert alert-danger" v-if="commentErrors && commentErrors.text">
                {{ commentErrors.text.join('. ') }}
              </div>
            </div>
            <div class="form-group">
              <label>Прикрепить фото (макс. 3)</label>
              <input type="file" id="photos" multiple accept="image/*" class="mb-2">
            </div>
            <div v-if="commentErrors && (commentErrors.photo || commentErrors.photo2 || commentErrors.photo3)" class="alert alert-danger">
              <div v-if="commentErrors.photo">{{ commentErrors.photo.join('. ') }}</div>
              <div v-if="commentErrors.photo2">{{ commentErrors.photo2.join('. ') }}</div>
              <div v-if="commentErrors.photo3">{{ commentErrors.photo3.join('. ') }}</div>
            </div>
            <button type="button" class="btn btn-primary" @click="submitComment">
              Отправить отзыв
            </button>
          </form>
        </div>

        <div class="mt-8">
          <div class="flex-align-center mb-4">
            <h2 class="flex-grow">Отзывы сотрудников</h2>
            <div v-if="selectedCategory" class="active-filter">
              <span>Категория: {{ getCategoryName(selectedCategory) }}</span>
              <span class="close-filter" @click="clearCategoryFilter">×</span>
            </div>
          </div>

          <div v-if="loading" class="text-center py-8">Загрузка отзывов...</div>
          <div v-else-if="comments.length === 0" class="card text-center py-8">
            <p>Отзывов в этой категории пока нет. Будьте первым!</p>
          </div>
          <div v-else>
            <div v-for="comment in comments" :key="comment.id" class="comment-card">
              <div class="comment-header">
                <img :src="comment.user && comment.user.avatar ? '/storage/' + comment.user.avatar : '/images/default_avatar.png'" class="comment-avatar-img" alt="Аватар" />
                <div class="comment-meta">
                  <span class="comment-author">
                    {{ comment.user ? (comment.user.full_name || comment.user.login) : 'Пользователь' }}
                  </span>
                  <span class="comment-time">{{ formatDate(comment.created_at) }}</span>
                </div>
                <span class="comment-category-badge">{{ comment.category ? comment.category.name : 'Категория' }}</span>
              </div>
              <p class="comment-text">{{ comment.text }}</p>
              <div v-if="comment.photos && comment.photos.length > 0" class="comment-photos mt-4">
                <img v-for="photo in comment.photos" :key="photo.id" :src="'/' + photo.photo_path" class="comment-photo" />
              </div>
              <div class="comment-actions mt-4">
                <button class="like-btn" :class="{ active: comment.is_liked }" @click="toggleLike(comment.id)">
                  Лайк <span class="likes-count">{{ comment.likes_count || 0 }}</span>
                </button>
                <a v-if="userInfo.id === comment.user_id" href="#" class="btn btn-secondary btn-small" @click.prevent="changePage('EditCommentPage', comment.id)">Редактировать</a>
                <button v-if="userInfo.id === comment.user_id" class="btn btn-secondary btn-small btn-danger-outline" @click="deleteComment(comment.id)">Удалить</button>
              </div>

              <!-- Ответ админа на главной -->
              <div v-if="comment.replies && comment.replies.length > 0" class="admin-reply mt-4">
                <div class="comment-header">
                  <img :src="comment.replies[0].user && comment.replies[0].user.avatar ? '/storage/' + comment.replies[0].user.avatar : '/images/default_avatar.png'" class="comment-avatar-img" alt="Аватар" />
                  <div class="comment-meta">
                    <span class="comment-author">
                      {{ comment.replies[0].user ? (comment.replies[0].user.full_name || comment.replies[0].user.login) : 'Администратор' }}
                    </span>
                    <span class="comment-time">{{ formatDate(comment.replies[0].created_at) }}</span>
                  </div>
                </div>
                <p class="comment-text reply-text">{{ comment.replies[0].text }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  props: ['changePage', 'datasend', 'userInfo'],
  data() {
    return {
      visibleCategories: [],
      selectedCategory: null,
      comments: [],
      loading: false,
      newComment: {
        category_id: null,
        text: ''
      },
      commentErrors: null,
      successMessage: ''
    };
  },
  mounted() {
    this.loadCategories();
    this.loadComments();
  },
  methods: {
    loadCategories() {
      this.datasend('categories')
        .then(res => {
          this.visibleCategories = res.data || res;
        });
    },
    loadComments() {
      this.loading = true;
      let route = this.selectedCategory ? `commentsFiltered/${this.selectedCategory}` : 'commentaries';
      this.datasend(route)
        .then(res => {
          this.comments = res.data || res;
          this.loading = false;
        });
    },
    submitComment() {
      this.commentErrors = null;
      this.successMessage = '';
      let fd = new FormData();
      if (this.newComment.text) fd.append('text', this.newComment.text);
      if (this.newComment.category_id) fd.append('category_id', this.newComment.category_id);
      
      let photos = document.querySelector('#photos');
      if (photos && photos.files) {
        for (let i = 0; i < Math.min(photos.files.length, 3); i++) {
          let fieldName = i === 0 ? 'photo' : (i === 1 ? 'photo2' : 'photo3');
          fd.append(fieldName, photos.files[i]);
        }
      }

      this.datasend('userCommAdd', 'POST', fd)
        .then(res => {
          if (res.errors) {
            this.commentErrors = res.errors;
          } else {
            this.newComment.text = '';
            if (photos) photos.value = '';
            this.loadComments();
            this.successMessage = 'Отзыв успешно отправлен!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          }
        });
    },
    getCategoryName(id) {
      const cat = this.visibleCategories.find(c => c.id === id);
      return cat ? cat.name : 'Выбранная категория';
    },
    selectCategory(id) {
      this.selectedCategory = id;
      this.newComment.category_id = id;
      this.loadComments();
    },
    clearCategoryFilter() {
      this.selectedCategory = null;
      this.newComment.category_id = null;
      this.loadComments();
    },
    scrollToCategories() {
      document.getElementById('categories')?.scrollIntoView({ behavior: 'smooth' });
    },
    scrollToComments() {
      document.getElementById('comments')?.scrollIntoView({ behavior: 'smooth' });
    },
    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('ru-RU');
    },
    toggleLike(commentId) {
      this.datasend(`like/${commentId}`, 'POST')
        .then(() => this.loadComments());
    },
    deleteComment(commentId) {
      if (confirm('Удалить отзыв?')) {
        this.datasend(`userCommDel/${commentId}`, 'DELETE')
          .then(() => this.loadComments());
      }
    }
  }
}
</script>
