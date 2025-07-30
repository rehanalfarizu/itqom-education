<template>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ formatDate(user.created_at) }}</td>
          <td>
            <button class="btn btn-sm btn-info" @click="viewUser(user.id)">
              <i class="fas fa-eye"></i> View
            </button>
            <button class="btn btn-sm btn-warning" @click="editUser(user.id)">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)">
              <i class="fas fa-trash"></i> Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'UserTable',
  props: {
    users: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    viewUser(id) {
      console.log('View user:', id);
    },
    editUser(id) {
      console.log('Edit user:', id);
    },
    deleteUser(id) {
      if (confirm('Are you sure you want to delete this user?')) {
        console.log('Delete user:', id);
      }
    }
  }
}
</script>
