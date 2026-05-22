<template>
  <div id="wrapper">
    <HeaderComponent 
      :changePage="changePage" 
      :user="user" 
      :logout="logout" 
      :datasend="datasend" 
      :userInfo="userInfo" 
    />
    <main class="main-content" style="min-height: 70vh;">
      <HomePage 
        v-if="page === 'HomePage'" 
        :changePage="changePage" 
        :datasend="datasend" 
        :userInfo="userInfo" 
      />
      <RegisterPage 
        v-if="page === 'RegisterPage'" 
        :datasend="datasend" 
        :changePage="changePage"
        :changeToken="changeToken" 
      />
      <LoginPage 
        v-if="page === 'LoginPage'" 
        :datasend="datasend" 
        :changePage="changePage" 
        :changeToken="changeToken" 
      />
      <ProfilePage 
        v-if="page === 'ProfilePage'" 
        :datasend="datasend" 
        :user="userInfo" 
        :userInfo="userInfo"
        :pageId="pageId" 
        :getUser="getUser" 
        :changePage="changePage" 
      />
      <AdminPage 
        v-if="page === 'AdminPage'" 
        :changePage="changePage" 
        :datasend="datasend" 
        :userInfo="userInfo" 
      />
      <EditCommentPage 
        v-if="page === 'EditCommentPage' && pageId" 
        :pageId="pageId" 
        :changePage="changePage"
        :datasend="datasend" 
      />
    </main>
    <FooterComponent />
  </div>
</template>

<script>
import HeaderComponent from './components/HeaderComponent.vue';
import FooterComponent from './components/FooterComponent.vue';
import HomePage from './pages/HomePage.vue';
import AdminPage from './pages/AdminPage.vue';
import ProfilePage from './pages/ProfilePage.vue';
import LoginPage from './pages/LoginPage.vue';
import RegisterPage from './pages/RegisterPage.vue';
import EditCommentPage from './pages/EditCommentPage.vue';

export default {
  name: 'App',
  components: {
    HeaderComponent,
    FooterComponent,
    HomePage,
    AdminPage,
    ProfilePage,
    LoginPage,
    RegisterPage,
    EditCommentPage
  },
  data() {
    return {
      page: localStorage.getItem('page') || 'HomePage',
      pageId: localStorage.getItem('pageId') || null,
      API: 'http://127.0.0.1:8000/api/',
      PUBLIC: 'http://127.0.0.1:8000/storage/',
      user: false,
      userInfo: {},
    }
  },
  mounted() {
    if (localStorage.getItem('token')) {
      this.getUser();
    }
  },
  methods: {
    getUser() {
      this.datasend('me')
        .then((response) => {
          if (response.id) {
            this.user = true;
            this.userInfo = response;
          } else {
            localStorage.removeItem('token');
            this.user = false;
            this.userInfo = {};
          }
        })
        .catch((error) => {
          console.log('error', error);
          localStorage.removeItem('token');
          this.user = false;
          this.userInfo = {};
        });
    },
    changePage(page, pageId = null) {
      this.page = page;
      this.pageId = pageId;
      localStorage.setItem('page', page);
      localStorage.setItem('pageId', pageId);
    },
    logout() {
      this.datasend('logout', 'POST')
        .then((result) => {
          localStorage.removeItem('token');
          this.changePage('HomePage');
          this.user = false;
          this.userInfo = {};
        })
        .catch((error) => console.error(error));
    },
    changeToken(token) {
      localStorage.setItem('token', token);
      this.user = true;
      this.getUser();
    },
    async datasend(route, method = 'GET', formdata = null) {
      let myHeaders = new Headers();
      myHeaders.append('Accept', 'application/json');

      if (localStorage.getItem('token')) {
        myHeaders.append('Authorization', 'Bearer ' + localStorage.getItem('token'));
      }

      let requestOptions = {
        method: method,
        headers: myHeaders,
        redirect: 'follow',
      };

      if (method !== 'GET') {
        requestOptions.body = formdata;
      }

      return await fetch(this.API + route, requestOptions).then(
        (response) => {
          return response.json();
        },
      );
    }
  },
}
</script>
