<template>
  <div class="container py-8">
    <h1 style="margin-bottom: 30px;">Личный кабинет</h1>

    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
    </div>

    <div class="card mb-8">
      <div class="profile-layout">
        <div class="profile-sidebar">
          <div class="avatar-wrapper">
            <img :src="avatarPreview || (displayUser.avatar ? '/storage/' + displayUser.avatar : '/images/default_avatar.png')" alt="Аватар" class="profile-avatar-large" />
            <a v-if="userInfo && displayUser && (userInfo.id === displayUser.id || userInfo.role === 'admin')" href="#" class="avatar-upload-overlay"
              @click.prevent="$refs.avatarInput.click()">
              <span>Выбрать фото</span>
            </a>
          </div>
          <input type="file" ref="avatarInput" id="avatar" style="display: none" accept="image/*" @change="handleAvatarChange" />
          <p v-if="avatarFile" class="text-muted mt-2 text-center" style="font-size: 0.8rem;">Фото выбрано. Нажмите "Обновить данные" для сохранения.</p>
        </div>
        <div class="profile-details">
          <form action="?" method="post">
            <div class="info-grid">
              <div class="info-item">
                <label>ФИО</label>
                <input v-model="full_name" type="text" placeholder="Иванов Иван Иванович" />
              </div>
              <div class="info-item">
                <label>Логин (нельзя изменить)</label>
                <div class="info-value">{{ displayUser.login }}</div>
              </div>
              <div class="info-item">
                <label>Электронная почта</label>
                <input v-model="email" type="email" placeholder="example@omk-it.ru" />
              </div>
              <div class="info-item">
                <label>Телефон</label>
                <input v-model="phone" type="tel" placeholder="+7(999)-999-99-99" />
              </div>
            </div>
            <div v-if="errors" class="mt-4">
              <div v-if="errors.full_name" class="alert alert-danger">{{ errors.full_name.join('. ') }}</div>
              <div v-if="errors.email" class="alert alert-danger">{{ errors.email.join('. ') }}</div>
              <div v-if="errors.phone" class="alert alert-danger">{{ errors.phone.join('. ') }}</div>
              <div v-if="errors.avatar" class="alert alert-danger">{{ errors.avatar.join('. ') }}</div>
            </div>
            <button v-if="userInfo && displayUser && (userInfo.id === displayUser.id || userInfo.role === 'admin')" type="button" class="btn btn-primary mt-4" @click="updateProfile">
              Обновить данные
            </button>
          </form>
        </div>
      </div>
    </div>

    <section class="section">
      <h2>История полученных услуг</h2>

      <div class="card mb-8">
        <h3>Добавить полученную услугу</h3>
        <form action="?" method="post">
          <div class="form-group">
            <label>Выберите категорию</label>
            <select v-model="newService.category_id">
              <option :value="null">Выберите категорию</option>
              <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <div class="alert alert-danger" v-if="serviceErrors && serviceErrors.category_id">
              {{ serviceErrors.category_id.join('. ') }}
            </div>
          </div>
          <div class="form-group">
            <label>Дата услуги</label>
            <input type="date" v-model="newService.service_date">
            <div class="alert alert-danger" v-if="serviceErrors && serviceErrors.service_date">
              {{ serviceErrors.service_date.join('. ') }}
            </div>
          </div>

          <button type="button" class="btn btn-primary" @click="addService">
            Добавить в историю
          </button>
        </form>
      </div>

      <div v-if="!displayUser.services || displayUser.services.length === 0" class="card text-center py-8">
        <p>Вы еще не добавили услуг в историю.</p>
      </div>
      <div v-else class="services-list">
        <div v-for="service in displayUser.services" :key="service.id" class="card">
          <div class="flex-align-center">
            <div class="flex-grow">
              <h4>{{ service.category ? service.category.name : 'Категория не указана' }}</h4>
              <p class="text-muted">{{ formatDate(service.service_date) }}</p>
            </div>
            <button type="button" class="btn btn-secondary btn-small"
              @click="removeService(service.id)">Удалить</button>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <h2>История отзывов</h2>
      <div v-if="!displayUser.comments || displayUser.comments.length === 0" class="card text-center py-8">
        <p>Вы еще не оставляли отзывов.</p>
      </div>
      <div v-else class="comments-list">
        <div v-for="comment in displayUser.comments" :key="comment.id" class="comment-card">
          <div class="comment-header">
            <img :src="displayUser.avatar ? '/storage/' + displayUser.avatar : '/images/default_avatar.png'" class="comment-avatar-img" alt="Аватар" />
            <div class="comment-meta">
              <span class="comment-author">{{ comment.category ? comment.category.name : 'Категория не указана' }}</span>
              <span class="comment-time">{{ formatDate(comment.created_at) }}</span>
            </div>
          </div>
          <p class="comment-text">{{ comment.text }}</p>
          <div class="comment-actions">
            <button type="button" class="btn btn-secondary btn-small"
              @click="changePage('EditCommentPage', comment.id)">Редактировать</button>
            <button type="button" class="btn btn-secondary btn-small btn-danger-outline"
              @click="deleteComment(comment.id)">Удалить</button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'ProfilePage',
  props: ['changePage', 'datasend', 'user', 'userInfo', 'getUser', 'pageId'],
  data() {
    return {
      categories: [],
      displayUser: {},
      full_name: '',
      email: '',
      phone: '',
      errors: null,
      newService: {
        category_id: null,
        service_date: new Date().toISOString().substr(0, 10)
      },
      serviceErrors: null,
      successMessage: '',
      avatarFile: null,
      avatarPreview: null
    }
  },
  computed: {
    availableCategories() {
      if (!this.displayUser.services) return this.categories;
      const addedCategoryIds = this.displayUser.services.map(s => s.category_id);
      return this.categories.filter(cat => !addedCategoryIds.includes(cat.id));
    }
  },
  mounted() {
    this.loadCategories();
    if (this.pageId && this.pageId != this.userInfo.id) {
      this.loadUserProfile();
    } else {
      this.displayUser = this.user;
      this.full_name = this.user.full_name || '';
      this.email = this.user.email || '';
      this.phone = this.user.phone || '';
    }
  },
  watch: {
    pageId(newVal) {
      if (newVal && newVal != this.userInfo.id) {
        this.loadUserProfile();
      } else {
        this.displayUser = this.user;
        this.full_name = this.user.full_name || '';
        this.email = this.user.email || '';
        this.phone = this.user.phone || '';
      }
    }
  },
  methods: {
    loadUserProfile() {
      this.datasend(`users/${this.pageId}`)
        .then(res => {
          this.displayUser = res;
          this.full_name = res.full_name || '';
          this.email = res.email || '';
          this.phone = res.phone || '';
        });
    },
    handleAvatarChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.avatarFile = file;
        this.avatarPreview = URL.createObjectURL(file);
      }
    },
    updateProfile() {
      this.errors = null;
      this.successMessage = '';
      let fd = new FormData();
      fd.append('_method', 'PUT');
      if (this.full_name) fd.append('full_name', this.full_name);
      if (this.email) fd.append('email', this.email);
      if (this.phone) fd.append('phone', this.phone);
      if (this.avatarFile) fd.append('avatar', this.avatarFile);

      const userId = this.displayUser.id;

      this.datasend(`users/${userId}`, 'POST', fd)
        .then((result) => {
          if (result.errors) {
            this.errors = result.errors;
          } else {
            if (userId == this.userInfo.id) this.getUser();
            else this.loadUserProfile();
            this.avatarFile = null;
            this.avatarPreview = null;
            this.successMessage = 'Данные профиля успешно обновлены!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          }
        });
    },
    loadCategories() {
      this.datasend('categories')
        .then(res => {
          this.categories = res.data || res;
        });
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString('ru-RU');
    },
    addService() {
      this.serviceErrors = null;
      let fd = new FormData();
      if (this.newService.category_id) fd.append('category_id', this.newService.category_id);
      if (this.newService.service_date) fd.append('service_date', this.newService.service_date);

      this.datasend(`userCatAdd/${this.displayUser.id}`, 'POST', fd)
        .then(res => {
          if (res.errors) {
            this.serviceErrors = res.errors;
          } else {
            if (this.displayUser.id == this.userInfo.id) this.getUser();
            else this.loadUserProfile();
            this.newService.category_id = null;
            this.successMessage = 'Услуга успешно добавлена!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          }
        });
    },
    removeService(id) {
      if (confirm('Удалить услугу из истории?')) {
        this.datasend(`userCatDel/${id}`, 'DELETE')
          .then(() => {
            if (this.displayUser.id == this.userInfo.id) this.getUser();
            else this.loadUserProfile();
          });
      }
    },
    deleteComment(id) {
      if (confirm('Удалить отзыв?')) {
        this.datasend(`userCommDel/${id}`, 'DELETE')
          .then(() => {
            if (this.displayUser.id == this.userInfo.id) this.getUser();
            else this.loadUserProfile();
          });
      }
    }
  }
}
</script>
