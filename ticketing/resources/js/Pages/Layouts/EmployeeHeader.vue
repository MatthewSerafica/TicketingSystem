<template>
  <nav>
    <div class="left">
      <a href="/employee" class="logo"></a>
      <a href="/employee" class="brand">TMDD Ticketing System</a>
    </div>
    <div class="right">
      <a href="#" class="profile-picture"></a>
      <a href="#" class="user-link">Employee</a>
    </div>
  </nav>
</template>

<script>
</script>


<style>
nav {
  background-color: var(--primary-color);
  color: #fff;
  padding: 10px;
  display: flex;
  justify-content: space-between;

}

.left,
.right {
  display: flex;
  align-items: center;
}

.logo,
.profile-picture {
  width: 30px;
  height: 30px;
  background-color: #ffffff;
  /* Gray color */
  display: block;
  margin-right: 10px;
  border-radius: 50%;
  /* Make it rounded */
}

.brand,
.user-link {
  color: var(--text-color);
  text-decoration: none;
  font-weight: bold;
  font-size: 16px;
}

.user-link {
  padding: 8px 15px;
  border-radius: 5px;

  transition: background-color 0.3s;
}

.user-link:hover {
  background-color: #777;
  /* Background color on hover */
}

.right .profile-picture {
  margin-right: 0;
  /* Remove margin for the profile picture */
}

.right .user-link {
  margin-right: 10px;
  /* Add margin for user link */
}</style>