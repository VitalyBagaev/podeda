<template>
  <div class="container py-8">
    <div class="flex-align-center mb-8">
      <h1 class="flex-grow">Панель администратора</h1>
    </div>

    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
    </div>

    <div class="admin-tabs mb-8">
      <button class="btn" :class="activeTab === 'comments' ? 'btn-primary' : 'btn-secondary'"
        @click="activeTab = 'comments'">Комментарии</button>
      <button class="btn" :class="activeTab === 'users' ? 'btn-primary' : 'btn-secondary'"
        @click="activeTab = 'users'">Пользователи</button>
    </div>

    <!-- Вкладка комментариев -->
    <div v-if="activeTab === 'comments'" class="admin-content">
      <div v-if="loadingComments" class="text-center py-8">Загрузка комментариев...</div>
      <div v-else-if="comments.length === 0" class="card text-center py-8">
        <p>Комментариев не найдено.</p>
      </div>
      <div v-else class="comments-list">
        <div v-for="comment in comments" :key="comment.id" class="comment-card">
          <div class="comment-header">
            <img :src="comment.user && comment.user.avatar ? '/storage/' + comment.user.avatar : '/images/default_avatar.png'"
                class="comment-avatar-img" alt="Аватар пользователя" />
            <div class="comment-meta">
              <span class="comment-author clickable" @click="changePage('ProfilePage', comment.user_id)">
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
            <button class="btn btn-secondary btn-small" @click="replyToComment(comment)">
              {{ comment.replies && comment.replies.length > 0 ? 'Редактировать ответ' : 'Ответить' }}
            </button>
            <a href="#" class="btn btn-secondary btn-small"
              @click.prevent="changePage('EditCommentPage', comment.id)">Редактировать</a>
            <button class="btn btn-secondary btn-small btn-danger-outline"
              @click="deleteComment(comment.id)">Удалить</button>
          </div>

          <!-- Форма ответа администратора -->
          <div v-if="replyingTo === comment.id" class="card mt-4" style="background-color: #f9fafb;">
            <h4>{{ editingReplyId ? 'Редактировать ответ администратора' : 'Ответить на комментарий' }}</h4>
            <form action="?" method="post">
              <div class="form-group">
                <label>Ваш ответ</label>
                <textarea v-model="replyText" rows="3" placeholder="Введите ответ администратора..."></textarea>
                <div class="alert alert-danger" v-if="replyErrors && replyErrors.text">
                  {{ replyErrors.text.join('. ') }}
                </div>
              </div>
              <div class="flex-align-center gap-2">
                <button type="button" class="btn btn-primary" @click="submitReply">
                  Сохранить ответ
                </button>
                <button type="button" class="btn btn-secondary" @click="cancelReplyForm">Отмена</button>
              </div>
            </form>
          </div>

          <!-- Ответ администратора (если есть) -->
          <div v-if="comment.replies && comment.replies.length > 0" class="admin-reply mt-4">
            <div class="comment-header">
              <img :src="comment.replies[0].user && comment.replies[0].user.avatar ? '/storage/' + comment.replies[0].user.avatar : '/images/default_avatar.png'"
                  class="comment-avatar-img" alt="Аватар администратора" />
              <div class="comment-meta">
                <span class="comment-author clickable" @click="changePage('ProfilePage', comment.replies[0].user_id)">
                  {{ comment.replies[0].user ? (comment.replies[0].user.full_name || comment.replies[0].user.login) : 'Администратор' }}
                </span>
                <span class="comment-time">{{ formatDate(comment.replies[0].created_at) }}</span>
              </div>
            </div>
            <p class="comment-text reply-text">{{ comment.replies[0].text }}</p>
            <div class="comment-actions mt-4">
              <button class="btn btn-secondary btn-small" @click="replyToComment(comment)">Редактировать ответ</button>
              <button class="btn btn-secondary btn-small btn-danger-outline"
                @click="deleteReply(comment.replies[0].id)">Удалить ответ</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Вкладка пользователей -->
    <div v-if="activeTab === 'users'" class="admin-content">
      <div v-if="loadingUsers" class="text-center py-8">Загрузка пользователей...</div>
      <div v-else-if="users.length === 0" class="card text-center py-8">
        <p>Пользователей не найдено.</p>
      </div>
      <div v-else class="card" style="padding: 0;">
        <div class="overflow-x-auto">
          <table class="users-table">
            <thead>
              <tr>
                <th>Пользователь</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Дата регистрации</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>
                  <div class="flex-align-center gap-2 clickable" @click="changePage('ProfilePage', user.id)">
                    <img :src="user.avatar ? '/storage/' + user.avatar : '/images/default_avatar.png'" class="user-avatar" alt="Аватар" />
                    <div>
                      <div class="font-weight-600">{{ user.full_name || user.login }}</div>
                      <div class="text-muted" style="font-size: 0.85rem;">@{{ user.login }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ user.email }}</td>
                <td>{{ user.phone || '-' }}</td>
                <td>
                  <span class="badge" :class="user.role === 'admin' ? 'badge-admin' : 'badge-user'">
                    {{ user.role === 'admin' ? 'Администратор' : 'Пользователь' }}
                  </span>
                </td>
                <td>{{ formatDate(user.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPage',
  props: ['user', 'datasend', 'changePage', 'userInfo'],
  data() {
    return {
      activeTab: 'comments',
      comments: [],
      users: [],
      loadingComments: false,
      loadingUsers: false,
      replyingTo: null,
      editingReplyId: null,
      replyText: '',
      replyErrors: {},
      successMessage: ''
    };
  },
  mounted() {
    this.loadComments();
    this.loadUsers();
  },
  methods: {
    loadComments() {
      this.loadingComments = true;
      this.datasend('commentaries')
        .then(res => {
          this.comments = res.data || res;
          this.loadingComments = false;
        });
    },
    loadUsers() {
      this.loadingUsers = true;
      this.datasend('users')
        .then(res => {
          this.users = res.users || res;
          this.loadingUsers = false;
        });
    },
    deleteComment(commentId) {
      if (confirm('Удалить комментарий?')) {
        this.datasend(`userCommDel/${commentId}`, 'DELETE')
          .then(() => {
            this.loadComments();
            this.successMessage = 'Комментарий удален!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          });
      }
    },
    replyToComment(comment) {
      const existingReply = comment.replies && comment.replies.length > 0 ? comment.replies[0] : null;
      this.replyingTo = comment.id;
      this.editingReplyId = existingReply ? existingReply.id : null;
      this.replyText = existingReply ? existingReply.text : '';
    },
    cancelReplyForm() {
      this.replyingTo = null;
      this.editingReplyId = null;
      this.replyText = '';
    },
    submitReply() {
      if (!this.replyText.trim()) return;
      this.replyErrors = {};
      let fd = new FormData();
      fd.append('text', this.replyText);
      
      const isEditing = Boolean(this.editingReplyId);
      const route = isEditing ? `answerEdit/${this.editingReplyId}` : `answer/${this.replyingTo}`;
      
      this.datasend(route, 'POST', fd)
        .then(res => {
          if (res.errors) {
            this.replyErrors = res.errors;
          } else {
            this.cancelReplyForm();
            this.loadComments();
            this.successMessage = 'Ответ успешно сохранен!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          }
        });
    },
    deleteReply(replyId) {
      if (confirm('Удалить ответ администратора?')) {
        this.datasend(`answerDel/${replyId}`, 'DELETE')
          .then(() => {
            this.loadComments();
            this.successMessage = 'Ответ удален!';
            setTimeout(() => { this.successMessage = ''; }, 5000);
          });
      }
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('ru-RU');
    }
  },
};
</script>
