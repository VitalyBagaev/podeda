<template>
  <header class="header">
    <div class="header-container">
      <a href="#" @click.prevent="changePage('HomePage')" class="logo-box">
        <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="logo-img" />
        <div class="logo-text">
          <span class="logo-it">ИТ</span> ОМК
        </div>
      </a>
      
      <nav class="header-nav"></nav>

      <div class="auth-buttons flex-align-center">
        <template v-if="!user">
          <a href="#" @click.prevent="changePage('LoginPage')" class="nav-link mr-4">Вход</a>
          <a href="#" @click.prevent="changePage('RegisterPage')" class="btn btn-primary btn-small">Регистрация</a>
        </template>
        <template v-else>
          <!-- Аватар и никнейм -->
          <a href="#" class="user-info-box mr-4" @click.prevent="changePage('ProfilePage')">
            <img
              :src="userInfo.avatar ? '/storage/' + userInfo.avatar : '/images/default_avatar.png'"
              class="header-avatar mr-3"
              alt="Профиль"
            >
            <span class="header-username">{{ userInfo.full_name || userInfo.login }}</span>
          </a>

          <!-- Админ-панель отображается как обычный пункт хедера -->
          <a
            v-if="userInfo && userInfo.role === 'admin'"
            href="#"
            @click.prevent="changePage('AdminPage')"
            class="nav-link mr-4"
          >Админ-панель</a>

          <!-- Выход -->
          <a href="#" @click.prevent="logout()" class="nav-link">Выход</a>
        </template>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'HeaderComponent',
  props: ['changePage', 'user', 'logout', 'datasend', 'userInfo'],
}
</script>

<style scoped>
.logo-box {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}
.logo-img {
  height: 44px;
  width: auto;
}
.logo-text {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--text-main);
  line-height: 1;
}
.logo-it {
  color: var(--primary-color);
}
.user-info-box {
  display: flex;
  align-items: center;
  cursor: pointer;
  text-decoration: none;
}
.header-username {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-main);
}
.header-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid var(--border-color);
}
.mr-3 { margin-right: 0.75rem; }
.mr-4 { margin-right: 1rem; }
</style>
