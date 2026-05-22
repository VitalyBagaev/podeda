<template>
  <header class="bg-danger border-bottom border-secondary sticky-top">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center py-3">
        <a href="#" @click.prevent="changePage('HomePage')" class="text-decoration-none text-white">
          <img src="/images/unique_omk_it_logo.png" alt="ИТ ОМК" class="img-fluid" style="height: 44px;" />
          <div class="d-inline-block align-middle ms-2">
            <span class="text-white fw-bold">ИТ</span> ОМК
          </div>
        </a>

        <nav class="d-flex gap-3 text-white">
          <!-- Навигация может быть добавлена здесь -->
        </nav>

        <div class="d-flex align-items-center gap-3">
          <template v-if="!user">
            <a href="#" @click.prevent="changePage('LoginPage')" class="text-decoration-none text-white fw-medium">Вход</a>
            <a href="#" @click.prevent="changePage('RegisterPage')" class="btn btn-light btn-sm text-danger">Регистрация</a>
          </template>
          <template v-else>
            <a href="#" class="d-flex align-items-center text-decoration-none text-white" @click.prevent="changePage('ProfilePage', null)">
              <img
                :src="userInfo.avatar ? PUBLIC + userInfo.avatar : '/images/default_avatar.png'"
                class="rounded-circle border border-secondary me-2"
                style="width: 36px; height: 36px;"
                alt="Профиль"
              />
              <span class="fw-medium">{{ userInfo.full_name || userInfo.login }}</span>
            </a>
            <a
              v-if="userInfo.role == 'admin'"
              href="#"
              @click.prevent="changePage('AdminPage')"
              class="text-decoration-none text-white fw-medium"
            >Админ-панель</a>
            <a href="#" @click.prevent="logout()" class="text-decoration-none text-white fw-medium">Выход</a>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'HeaderComponent',
  props: ['changePage', 'user', 'logout', 'datasend', 'userInfo', 'PUBLIC'],
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
