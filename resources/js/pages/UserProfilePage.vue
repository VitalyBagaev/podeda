<template>
  <div class="profile-page">
    <h1 class="page-title">{{ isOwnProfile ? 'Личный кабинет' : 'Профиль пользователя' }}</h1>
    <div class="profile-header card p-6 mb-8">
      <div class="profile-avatar-section">
        <div class="profile-avatar">
          <img :src="profileUser.avatar ? '/' + profileUser.avatar : '/images/default_avatar.png'"
            style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
        </div>
        <div v-if="isOwnProfile" class="avatar-controls mt-4">
          <input type="file" @change="onAvatarSelect" accept="image/*" class="mb-2">
          <button class="btn btn-secondary btn-small" @click="uploadAvatar" :disabled="!selectedAvatar">Загрузить
            фото</button>
        </div>
      </div>
      <div class="profile-info">
        <div class="profile-field mb-4">
          <label class="block font-bold mb-1">ФИО</label>
          <input v-if="isOwnProfile" v-model="profileUser.full_name" type="text" class="form-input">
          <span v-else>{{ profileUser.full_name }}</span>
        </div>
        <div class="profile-field mb-4">
          <label class="block font-bold mb-1">Email</label>
          <input v-if="isOwnProfile" v-model="profileUser.email" type="email" class="form-input">
          <span v-else>{{ profileUser.email }}</span>
        </div>
        <div class="profile-field mb-4">
          <label class="block font-bold mb-1">Телефон</label>
          <input v-if="isOwnProfile" v-model="profileUser.phone" type="tel" class="form-input">
          <span v-else>{{ profileUser.phone }}</span>
        </div>
        <div class="profile-actions mt-6">
          <button v-if="isOwnProfile" class="btn btn-primary" @click="updateProfile">Сохранить данные</button>
          <button class="btn btn-secondary ml-2" @click="changePage('HomePage')">Назад</button>
        </div>
      </div>
    </div>
    <div v-if="isOwnProfile" class="services-section mb-8">
      <h3 class="section-title mb-4">Полученные услуги</h3>
      <div class="card p-6 mb-4">
        <div class="form-row flex gap-4 mb-4">
          <div class="form-group flex-1">
            <label class="block mb-1">Категория услуги</label>
            <select v-model="newService.category_id" class="form-input">
              <option value="">Выберите категорию</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div class="form-group flex-1">
            <label class="block mb-1">Дата получения</label>
            <input type="date" v-model="newService.service_date" class="form-input">
          </div>
          <div class="flex items-end">
            <button class="btn btn-primary" @click="addService"
              :disabled="submittingService || !newService.category_id">Добавить</button>
          </div>
        </div>
        <div class="services-list">
          <div v-for="service in profileUser.services" :key="service.id"
            class="service-item flex justify-between items-center p-2 border-b">
            <span>{{ service.category ? service.category.name : 'Категория не указана' }}</span>
            <span>{{ formatDate(service.service_date) }}</span>
            <button class="btn btn-danger btn-small" @click="removeService(service.id)">Удалить</button>
          </div>
        </div>
      </div>
    </div>
    <div class="comments-section">
      <h3 class="section-title mb-4">Комментарии пользователя</h3>
      <div class="comments-list">
        <div v-for="comment in profileUser.comments" :key="comment.id" class="comment card p-4 mb-4">
          <div class="comment-header flex justify-between mb-2">
            <span class="font-bold">{{ comment.category ? comment.category.name : 'Услуга' }}</span>
            <span class="text-gray-500">{{ formatDate(comment.created_at) }}</span>
          </div>
          <div class="comment-text mb-4">{{ comment.text }}</div>
          <div v-if="comment.photos && comment.photos.length" class="comment-photos flex gap-2">
            <img v-for="photo in comment.photos" :key="photo.id" :src="'/' + photo.photo_path"
              class="w-24 h-24 object-cover rounded">
          </div>
        </div>
        <div v-if="!profileUser.comments || !profileUser.comments.length" class="text-center text-gray-500">
          Комментариев пока нет.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user', 'datasend', 'changePage', 'getUser', 'pageId'],
  data() {
    return {
      profileUser: {
        full_name: null,
        email: null,
        phone: null,
        avatar: null,
        services: [],
        comments: [],
      },
      selectedAvatar: null,
      categories: [],
      submittingService: false,
      newService: {
        category_id: null,
        service_date: new Date().toISOString().substr(0, 10)
      }
    }
  },
  computed: {
    isOwnProfile() {
      return !this.pageId || (this.user && this.user.id === parseInt(this.pageId));
    }
  },
  mounted() {
    this.loadProfile();
    this.loadCategories();
  },
  methods: {
    loadProfile() {
      const id = this.pageId || (this.user ? this.user.id : null);
      if (!id) return;

      this.datasend(`userShow/${id}`, 'GET')
        .then(res => {
          this.profileUser = res;
        })
        .catch(e => console.error(e));
    },
    loadCategories() {
      this.datasend('categories', 'GET')
        .then(res => {
          this.categories = res.data || res;
        })
        .catch(e => console.error(e));
    },
    onAvatarSelect(e) {
      this.selectedAvatar = e.target.files[0];
    },
    uploadAvatar() {
      if (!this.selectedAvatar) return;
      const formData = new FormData();
      formData.append('avatar', this.selectedAvatar);

      const id = this.user?.id;

      this.datasend(`users/${id}`, 'POST', formData)
        .then(() => {
          alert('Аватар обновлен');
          this.selectedAvatar = null;
          this.loadProfile();
          this.getUser();
        })
        .catch(e => console.error(e));
    },
    updateProfile() {
      const id = this.user?.id;

      this.datasend(`users/${id}`, 'PUT', {
        full_name: this.profileUser.full_name,
        email: this.profileUser.email,
        phone: this.profileUser.phone,
      })
        .then(() => {
          alert('Профиль обновлён');
          this.getUser();
        })
        .catch(e => console.error(e));
    },
    addService() {
      if (!this.newService.category_id) return;
      this.submittingService = true;
      const userId = this.user?.id;

      this.datasend(`userCatAdd/${userId}`, 'POST', this.newService)
        .then((result) => {
          if (result && result.errors) {
            alert(JSON.stringify(result.errors));
            return;
          }
          this.newService.category_id = '';
          this.loadProfile();
          this.getUser();
          alert('Услуга успешно добавлена в историю!');
        })
        .catch(err => console.error('Ошибка добавления:', err))
        .finally(() => {
          this.submittingService = false;
        });
    },
    removeService(id) {
      if (!confirm('Удалить эту услугу?')) return;

      this.datasend(`userCatDel/${id}`, 'DELETE')
        .then(() => {
          this.loadProfile();
        })
        .catch(e => console.error(e));
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('ru-RU');
    }
  }
}
</script>