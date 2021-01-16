<template>
    <li class="dropdown" v-show="notifications.length">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-bell"></span>
        </a>

        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a href="#">Foobar</a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                notifications : ['foobar']
            }
        },

        created() {
            axios.get("/profiles");
        },
    }
</script>