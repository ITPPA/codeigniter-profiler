<table>
	<?php if(isset($profile['header'])): ?>
		<tr>
			<?php foreach($profile['header'] as $th): ?>
				<th><?php echo $th ?></th>
			<?php endforeach ?>
		</tr>
	<?php endif ?>
	<?php foreach ($profile['data'] as $row): ?>
		<tr>
			<td><?php echo $row['field'] ?></td>
			<td><?php echo $row['data'] ?></td>
		</tr>
	<?php endforeach ?>
</table>

<?php if(isset($profile['sub'])): ?>
	<?php foreach($profile['sub'] as $sub): ?>
		<h3 class="codeigniter-profiler-sub-section-heading"><?php echo $sub['title'] ?></h3>
		<div class="codeigniter-profiler-sub-section">
		<?php if(empty($sub['data'])): ?>
			<p class="important"><?php echo lang('profiler_no_'.$sub['section']) ?></p>
		<?php else: ?>
			<table>
				<?php if(isset($sub['header'])): ?>
					<tr>
						<?php foreach($sub['header'] as $th): ?>
							<th><?php echo $th ?></th>
						<?php endforeach ?>
					</tr>
				<?php endif ?>
				<?php foreach ($sub['data'] as $row): ?>
					<tr>
						<td><?php echo $row['field'] ?></td>
						<td><?php echo $row['data'] ?></td>
					</tr>
				<?php endforeach ?>
			</table>
		<?php endif ?>
		</div>
	<?php endforeach ?>
<?php endif ?>