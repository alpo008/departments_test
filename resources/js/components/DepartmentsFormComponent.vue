<template>
    <div class="card">
        <div class="card-header">Form Component</div>

        <div class="card-body">
            I'm an form component.
        </div>
    </div>
</template>

<script>
export default {
    name: "DepartmentsFormComponent",
    data() {
        return {
            indexUrl: '/department',
            department: {},
        };
    },
}
</script>

<style scoped>

</style>
